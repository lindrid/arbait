<?php

namespace App\Http\Controllers;

use App\InstaIncome;
use App\InstaOutcome;
use App\Application;
use App\AccCalculations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use App\User;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use PhpParser\ErrorHandler\Collecting;
use Illuminate\Support\Facades\Storage;


class CInstaAccountancy extends Controller
{
    public function index($date = 'today')
    {
        if ($date == 'month') {
            return $this->indexMonth();
        }
        else {
            if ($date == 'today') {
                $date = Carbon::today()->toDateString();
            }

            $apps = InstaIncome::whereDate('date', $date)->get();
            $dayApplicationCount = 0;
            foreach($apps as $incomeRow) {
                if ($this->isItApplication($incomeRow->text)) {
                    $dayApplicationCount++;
                }
            }

            return [
                'apps'      => $apps,
                'date'      => $date,
                'appCount'  => $dayApplicationCount
            ];
        }
    }

    public function indexMonth()
    {
        $collection = array();
        $incomeMonthRows = InstaIncome::whereDate(
            'date', '>=', '2019-01-01')
            ->whereDate( 'date', '<=', Carbon::today())
            ->orderBy('date', 'asc')
            ->get();

        $outcomeMonthRows = InstaOutcome::whereDate(
            'date', '>=', '2019-01-01')
            ->whereDate( 'date', '<=', Carbon::today())
            ->orderBy('date', 'asc')
            ->get();

        $time = [];
        $time['income/outcome queries'] = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];

        $incomeMonth = $outcomeMonth = array();
        $dayCountMonth = $profitMonth = array();
        $monthApplicationCount = array();
        /*for ($i = 1; $i <= 12; $i++) {
            $incomes = Income::whereMonth('date', '=', $i)
                ->get();
            $outcomes = InstaOutcome::whereMonth('date', '=', $i)
                ->get();
            $incomeMonth[$i] = $incomes->sum('money_amount');
            $outcomeMonth[$i] = $outcomes->sum('money_amount');
            $dayCountMonth[$i] = $outcomes->count();
            $monthApplicationCount[$i] = $incomes->count();

            $profitMonth[$i] = $incomeMonth[$i] - $outcomeMonth[$i];
        }*/

        $incomeArr = $dayApplicationCount = array();
        if (count($incomeMonthRows) > 0) {
            foreach($incomeMonthRows as $incomeRow) {
                $iDate = Carbon::parse($incomeRow->date);
                $ymDate = $iDate->format('Y/m');
                $iDate = $iDate->format('Y/m/d');

                if (array_key_exists($ymDate, $incomeMonth)) {
                    $monthApplicationCount[$ymDate]++;
                    $incomeMonth[$ymDate] += $incomeRow->money_amount;
                }
                else {
                    $incomeMonth[$ymDate] = $incomeRow->money_amount;
                    $monthApplicationCount[$ymDate] = 1;
                }

                if (!array_key_exists($iDate, $incomeArr)) {
                    $incomeArr[$iDate] = $incomeRow->money_amount;
                    if ($this->isItApplication($incomeRow->text)) {
                        $dayApplicationCount[$iDate] = 1;
                    }
                }
                else {
                    $incomeArr[$iDate] += $incomeRow->money_amount;
                    if ($this->isItApplication($incomeRow->text)) {
                        if (array_key_exists($iDate, $dayApplicationCount)) {
                            $dayApplicationCount[$iDate]++;
                        }
                        else {
                            $dayApplicationCount[$iDate] = 1;
                        }
                    }
                }
            }
        }

        $time['foreach income rows'] = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];

        if (count($incomeMonthRows) > 0) {
            foreach($incomeArr as $iDate => $money_amount){
                $outcomeWithSameDate = false;
                foreach ($outcomeMonthRows as $outcomeRow) {
                    $oDate = Carbon::parse($outcomeRow->date);
                    $oDate = $oDate->format('Y/m/d');

                    if ($iDate == $oDate) {
                        $outcomeWithSameDate = true;

                        $a1 = new AccCalculations([
                            'title' => 'Доход: ' . $money_amount,
                            'date' => $iDate
                        ]);
                        array_push($collection, $a1);

                        $a2 = new AccCalculations([
                            'title' => 'Расход: ' . $outcomeRow->money_amount,
                            'date' => $iDate
                        ]);
                        array_push($collection, $a2);

                        $pr = $money_amount - $outcomeRow->money_amount;
                        $a3 = new AccCalculations([
                            'title' => 'Прибыль: ' . $pr,
                            'date' => $iDate
                        ]);
                        if ($pr < 0) {
                            $a1->customClass = 'color: red';
                        }
                        array_push($collection, $a3);

                        $ac = $dayApplicationCount[$iDate];
                        $a4 = new AccCalculations([
                            'title' => 'Заявок шт: ' . $ac,
                            'date' => $iDate
                        ]);
                        array_push($collection, $a4);

                        break;
                    }
                }
                if ($outcomeWithSameDate == false) {
                    $a1 = new AccCalculations([
                        'title' => 'Доход: ' . $money_amount,
                        'date' => $iDate
                    ]);
                    array_push($collection, $a1);

                    $a2 = new AccCalculations([
                        'title' => 'Расход: ' . 0,
                        'date' => $iDate
                    ]);
                    array_push($collection, $a2);

                    $pr = $money_amount;
                    $a3 = new AccCalculations([
                        'title' => 'Прибыль: ' . $pr,
                        'date' => $iDate
                    ]);
                    array_push($collection, $a3);

                    $ac = $dayApplicationCount[$iDate];
                    $a4 = new AccCalculations([
                        'title' => 'Заявок шт: ' . $ac,
                        'date' => $iDate
                    ]);
                    array_push($collection, $a4);
                }
            }
        }

        $time['foreach incomeArr / foreach outcome rows '] =
            microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];

        foreach($outcomeMonthRows as $outcomeRow) {
            $ymDate = Carbon::parse($outcomeRow->date);
            $ymDate = $ymDate->format('Y/m');
            if (array_key_exists($ymDate, $outcomeMonth)) {
                $outcomeMonth[$ymDate] += $outcomeRow->money_amount;
            }
            else {
                $outcomeMonth[$ymDate] = $outcomeRow->money_amount;
            }
            if (array_key_exists($ymDate, $incomeMonth)) {
                $profitMonth[$ymDate] = $incomeMonth[$ymDate] - $outcomeMonth[$ymDate];
            }

            $noMatches = true;
            foreach ($incomeArr as $iDate => $money_amount) {
                //$iDate = Carbon::parse($iDate);
                //$iDate = $iDate->format('Y/m/d');
                if ($oDate == $iDate) {
                    $noMatches = false;
                    break;
                }
            }
            if ($noMatches) {
                $a1 = new AccCalculations([
                    'title' => 'Доход: ' . 0,
                    'date' => $oDate
                ]);
                array_push($collection, $a1);
                $a1->customClass = 'color: red';

                $a2 = new AccCalculations([
                    'title' => 'Расход: ' . $money_amount,
                    'date' => $oDate
                ]);
                array_push($collection, $a2);

                $pr = 0 - $money_amount;
                $a3 = new AccCalculations([
                    'title' => 'Прибыль: ' . $pr,
                    'date' => $oDate
                ]);
                array_push($collection, $a3);

                $a4 = new AccCalculations([
                    'title' => 'Заявок шт: ' . 0,
                    'date' => $oDate
                ]);
                array_push($collection, $a4);
            }
        }

        $time['foreach outcome rows / foreach income rows WITH BREAKS '] =
            microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        //dd ($collection);
        //return;
        return response([
            'time'              => $time,
            'calcs'             => $collection,
            'incomes'           => $incomeMonth,
            'outcomes'          => $outcomeMonth,
            'profits'           => $profitMonth,
            'day_counts'        => $dayCountMonth,
            'month_app_count_array' => $monthApplicationCount,
            'month_names' => array (
                1   => 'январь',
                2   => 'февраль',
                3   => 'март',
                4   => 'апрель',
                5   => 'май',
                6   => 'июнь',
                7   => 'июль',
                8   => 'август',
                9   => 'сентябрь',
                10   => 'октябрь',
                11   => 'ноябрь',
                12   => 'декабрь'
            )
        ], 200);
        /*
        return [
            'insta_income' => $incomeMonthRows,
            'insta_outcome' => $outcomeMonthRows,
            'carbon' => Carbon::today()->toDateString()
        ];*/
    }



   /* public function indexUser()
    {
        $user = JWTAuth::parseToken()->authenticate()->name;
        $posts = Company::where("user_name", "=", $user)->get();

        return response()->json($posts);
    }*/

    public function show($id)
    {
        return Company::findOrFail($id);

    }

    /*public function show(Request $request, Company $post)
    {
        $user = JWTAuth::parseToken()->authenticate()->name;
        $posts = Company::where("user_names", "=", $user->name)->get();

        return response()->json($posts);
    }*/

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->update($request->all());

        return $company;
    }

    public function store(Request $request)
    {
        //return response ($request, 400);
        $applicationIncome = false;
        $date = $request->get('date');
        $dateCarbon = new Carbon($date);
        $year = $dateCarbon->year;
        $month = $dateCarbon->month;

        if (is_numeric($request->get('income')))
        {
            if ($request->get('income') < 0) {
                return response([
                    'error' => true,
                    'errors' => array(
                        'income' => 'доход не может быть отрицательным числом'
                    )
                ], 400);
            }

            if ((trim($request->get('text')) == '') && ($request->get('income') > 0))
            {
                return response([
                    'error' => true,
                    'errors' => array(
                        'text' => 'текст заявки не должен быть пустым'
                    )
                ], 400);
            }

            $applicationIncome = $request->get('income');
            $text = trim($request->get('text'));
            if ($text !== '') {
                try {
                    DB::beginTransaction();
                    $now = Carbon::now();
                   // $date = Carbon::today()->toDateString();
                    $affectedRows = DB::table('insta_income')
                        ->where('text', $text)->where('date', $date)
                        ->where('user_id', $request->get('designer_id'))
                        ->update([
                            'user_id' => $request->get('designer_id'),
                            'money_amount' => $applicationIncome,
                            'updated_at' => $now
                        ]);
                    if ($affectedRows == 0) {
                        DB::table('insta_income')->insert(
                            [
                                'user_id' => $request->get('designer_id'),
                                'text' => $text,
                                'date' => $date,
                                'was_parsed' => $request->get('was_parsed'),
                                'money_amount' => $applicationIncome,
                                'created_at' => $now,
                                'updated_at' => $now
                            ]
                        );
                    }
                    $howMuchCreated = InstaIncome::where('text', $text)->where('date', $date)->count();
                    if ($howMuchCreated > 1) {
                        InstaIncome::where('text', $text)->orderBy('id')->first()->delete();
                    }
                    DB::commit();
                }
                catch (QueryException $e) {
                    report($e);
                    DB::rollBack();

                    return response([
                        'error' => true,
                        'errors' => array (
                            'main' => "Возникла ошибка в работе с базой данных, обратитесь к администратору"
                        )
                    ], 400);
                }
            }
        }

        if (is_numeric($request->get('outcome')))
        {
            if ($request->get('outcome') < 0) {
                return response([
                    'error' => true,
                    'errors' => array(
                        'outcome' => 'расход не может быть отрицательным числом'
                    )
                ], 400);
            }

            DB::beginTransaction();
            try {
                $now = Carbon::now();
                //$date = Carbon::today()->toDateString();

                $affectedRows = DB::table('insta_outcome')
                    ->where([
                        ['date', '=', $date],
                        ['user_id', '=', $request->get('designer_id')]
                    ])
                    ->update([
                        'money_amount' => $request->get('outcome'),
                        'updated_at' => $now
                    ]);

                if ($affectedRows == 0) {
                    DB::table('insta_outcome')->insert([
                            'user_id' => $request->get('designer_id'),
                            'date' => $date,
                            'money_amount' => $request->get('outcome'),
                            'created_at' => $now,
                            'updated_at' => $now
                        ]
                    );
                }

                /*$howMuchCreated = InstaOutcome::where('date', $date)->count();
                if ($howMuchCreated > 1) {
                    InstaOutcome::where('date', $date)->first()->delete();
                }*/
                DB::commit();
            }
            catch (QueryException $e) {
                report($e);
                DB::rollBack();
                return response([
                    'error' => true,
                    'errors' => array (
                        'main' => "Возникла ошибка в работе с базой данных, обратитесь к администратору"
                    )
                ], 400);
            }
        }

        $income = array(
            'day'   => $this->getIncomeDay($date),
            'day_gleb' => $this->getIncomeDay($date, User::getGlebId()),
            'day_dima' => $this->getIncomeDay($date, User::getDimaId()),
            'month' => $this->getIncomeMonth(true),
            'year'  => $this->getIncomeYear(true)
        );

        $outcome = array(
            'day'   => $this->getOutcomeDay($date),
            'day_gleb' => $this->getOutcomeDay($date, User::getGlebId()),
            'day_dima' => $this->getOutcomeDay($date, User::getDimaId()),
            'month' => $this->getOutcomeMonth(),
            'year'  => $this->getOutcomeYear()
        );

        $glebMustSend = ($income['day_gleb'] - $income['day_dima'] -
                $outcome['day_gleb'] + $outcome['day_dima']) / 2;
        $dimaMustSend = 0;
        if ($glebMustSend < 0) {
            $dimaMustSend = abs($glebMustSend);
            $glebMustSend = 0;
        }

        $respArr = [
            'day_income'         => $income['day'],
            'day_income_gleb'    => $income['day_gleb'],
            'day_income_dima'    => $income['day_dima'],
            'gleb_must_send'   => $glebMustSend,
            'dima_must_send'   => $dimaMustSend,
            'day_outcome'   => $outcome['day'],
            'day_profit'    => $income['day'] - $outcome['day'],
            'month_income'      => $income['month']['summ'],
            //'month_income_gleb' => $income['month']['summ_gleb'],
            //'month_income_dima' => $income['month']['summ_dima'],
            'month_outcome'     => $outcome['month'],
            'month_profit'      => $income['month']['summ'] - $outcome['month'],
            'month_day_count'   => $income['month']['day_count'],
            'year_income'           => $income['year']['summ'],
            'year_outcome'          => $outcome['year'],
            'year_profit'           => $income['year']['summ'] - $outcome['year'],
            'year_day_count'        => $income['year']['day_count'],
        ];

        if ($applicationIncome !== false) {
            $respArr['application_income'] = $applicationIncome;
        }

        return response($respArr, 200);
    }

    public function destroy($id)
    {
        $income = InstaIncome::findOrFail($id);
        $income->delete();
        return '';
    }

    public function getPicNum()
    {
        $row = DB::table('insta_income')->latest()->first();
        if ($row) {
            $picNum = $row->id - 5 + 1;
        }
        else {
            $picNum = 1;
        }
        return response([
            'pic_num' =>  $picNum
        ], 200);
    }

    public function edit($id)
    {
        //
    }

    public function create()
    {
        //
    }

    private function getIncomeDay($date, $designer = 'all')
    {
        if ($designer == 'all') {
            $rowsToday = InstaIncome::whereDate('date', $date)->get();
        }
        else if (is_numeric($designer)) {
            $rowsToday = InstaIncome::whereDate('date', $date)->
                where('user_id', '=', $designer)->get();
        }

        $todayIncome = 0;
        foreach ($rowsToday as $incomeRow) {
            $todayIncome += $incomeRow->money_amount;
        }
        return $todayIncome;
    }

    private function getOutcomeDay($date, $designer = 'all') {

        if ($designer == 'all') {
            $todayOutcomeRows = InstaOutcome::whereDate('date', $date)->get();
        }
        else if (is_numeric($designer)) {
            $todayOutcomeRows = InstaOutcome::whereDate('date', $date)->
                where('user_id', '=', $designer)->get();
        }

        $todayOutcome = 0;

        if (count($todayOutcomeRows) > 0) {
            foreach($todayOutcomeRows as $outcomeRow){
                $todayOutcome += $outcomeRow->money_amount;
            }
        }
        return $todayOutcome;
    }

    private function getIncomeSpecificMonth($needToCountDays, $month, $year, $designer = 'all')
    {

    }

    private function getIncomeMonth($needToCountDays, $designer = 'all')
    {
        $designerName = '';

        if ($designer == 'all') {
            $allRowsMonth = InstaIncome::whereDate(
                'date', '>=', Carbon::now()->firstOfMonth()->toDateTimeString()
            )->whereDate( 'date', '<=', Carbon::now()->lastOfMonth()->toDateTimeString())->get();
        }
        else if (is_numeric($designer)) {
            $allRowsMonth = InstaIncome
                ::whereDate('date', '>=', Carbon::now()->firstOfMonth()->toDateTimeString())
                ->whereDate('date', '<=', Carbon::now()->lastOfMonth()->toDateTimeString())
                ->where('user_id', '=', $designer)
                ->get();

            if ($designer == User::getDimaId()) {
                $designerName = 'dima';
            }

            if ($designer == User::getDashaId()) {
                $designerName = 'dasha';
            }
        }

        $thisMonthIncome = 0;
        $dates = array();
        $firstTime = true;
        foreach($allRowsMonth as $incomeRow2){
            $thisMonthIncome += $incomeRow2->money_amount;
            if ($needToCountDays) {
                if ($firstTime) {
                    $d = new Carbon($incomeRow2->date);
                    array_push($dates, $d->toDateString());
                    $firstTime = false;
                } else {
                    $newDateTime = new Carbon($incomeRow2->date);
                    $na = array();
                    foreach ($dates as $dt) {
                        if ($newDateTime->toDateString() != $dt) {
                            array_push($na, $newDateTime->toDateString());
                        }
                    }
                    $dates = array_merge($dates, $na);
                    $dates = array_unique($dates);
                }
            }
        }

        $result = ['summ' . $designerName => $thisMonthIncome];

        if ($needToCountDays) {
            $uniqDates = array_unique($dates);
            $result['day_count'] = count($uniqDates);
        }
        return $result;
    }

    private function getOutcomeMonth() {
        $monthOutcomeRows = InstaOutcome::whereDate(
            'date', '>=', Carbon::now()->firstOfMonth()->toDateTimeString()
        )->whereDate( 'date', '<=', Carbon::now()->lastOfMonth()->toDateTimeString())->get();
        $thisMonthOutcome = 0;

        if (count($monthOutcomeRows) > 0) {
            foreach($monthOutcomeRows as $outcomeRow2){
                $thisMonthOutcome += $outcomeRow2->money_amount;
            }
        }
        return $thisMonthOutcome;
    }

    private function getIncomeYear($needToCountDays)
    {
        $allRowsYear = InstaIncome::whereDate(
            'date', '>=', '2019-01-01'
        )->whereDate( 'date', '<=', Carbon::today())->get();
        $thisYearIncome = 0;

        $dates = array();
        $firstTime = true;
        foreach($allRowsYear as $incomeRow2){
            $thisYearIncome += $incomeRow2->money_amount;
            if ($needToCountDays) {
                if ($firstTime) {
                    $d = new Carbon($incomeRow2->date);
                    array_push($dates, $d->toDateString());
                    $firstTime = false;
                } else {
                    $newDateTime = new Carbon($incomeRow2->date);
                    $na = array();
                    foreach ($dates as $dt) {
                        if ($newDateTime->toDateString() != $dt) {
                            array_push($na, $newDateTime->toDateString());
                        }
                    }
                    $dates = array_merge($dates, $na);
                    $dates = array_unique($dates);
                }
            }
        }

        $result = ['summ' => $thisYearIncome];

        if ($needToCountDays) {
            $uniqDates = array_unique($dates);
            $result['day_count'] = count($uniqDates);
        }
        return $result;
    }

    private function getOutcomeYear() {
        $yearOutcomeRows = InstaOutcome::whereDate(
            'date', '>=', '2019-01-01'
        )->whereDate( 'date', '<=', Carbon::today())->get();
        $thisYearOutcome = 0;

        if (count($yearOutcomeRows) > 0) {
            foreach($yearOutcomeRows as $outcomeRow2){
                $thisYearOutcome += $outcomeRow2->money_amount;
            }
        }
        return $thisYearOutcome;
    }


    private function isItApplication($applicationText)
    {
        return (
            1//(mb_stripos($applicationText,'жемчужина', 0, 'utf-8') === false)
        );
    }

    private function traceError(array $a, $funcName) {
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "=======================");
        $dateTime = Carbon::now()->toDayDateTimeString();
        Storage::append('error.txt', "$dateTime, Error in CAccountancy.". $funcName);
        Storage::append('error.txt', "=======================");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', implode(",", $a));
    }

}