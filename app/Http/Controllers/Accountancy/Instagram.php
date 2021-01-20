<?php

namespace App\Http\Controllers\Accountancy;

use App\Activity;
use App\Application;
use App\AccCalculations;
use App\Instagram\Income;
use App\Instagram\IPublic;

use App\Http\Controllers\Controller;

use App\Instagram\Outcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use App\User;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Mockery\Exception;
use PhpParser\ErrorHandler\Collecting;
use Illuminate\Support\Facades\Storage;


class Instagram extends Controller
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

            $pics = Income::whereDate('date', $date)->get();
            $picsCount = count($pics);

            return [
                'pics'      => $pics,
                'date'      => $date,
                'pics_count'  => $picsCount
            ];
        }
    }

    public function indexMonth()
    {
        $collection = array();
        $incomeMonthRows = Income::whereDate(
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

    public function getPublics()
    {
        try {
            $ps = IPublic::all();
            $income_arr[IPublic::ID_RABOTA_VDK] = IPublic::VAL_RABOTA_VDK;
            $income_arr[IPublic::ID_MODELS_VDK] = IPublic::VAL_MODELS_VDK;
        }
        catch (Exception $exception) {

        }
        return response([
            'publics' => $ps,
            'ID_RABOTA_VDK' => IPublic::ID_RABOTA_VDK,
            'ID_MODELS_VDK' => IPublic::ID_MODELS_VDK,
            'income_arr'    => $income_arr
        ],200);
    }

    public function store(Request $request)
    {
        //return response ($request, 400);
        $date = $request->get('date');
        $publicId = $request->get('public_id');
        $applicationIncome = $request->get('income');
        $applicationOutcome = $request->get('outcome');

        if (!$publicId && isset($applicationIncome)) {
            return response([
                'error' => true,
                'errors' => array(
                    'main' => 'Укажите паблик!'
                )
            ], 400);
        }
        //return response ($request, 400);
        $publics = IPublic::all();

        if (is_numeric($applicationIncome) && (!isset($applicationOutcome)))
        {
            if ($applicationIncome < 0) {
                return response([
                    'error' => true,
                    'errors' => array(
                        'income' => 'доход не может быть отрицательным числом'
                    )
                ], 400);
            }

            try {
                DB::beginTransaction();
                $rowIncome = new Income();
                $rowIncome->activity_id = Activity::ID_INSTAGRAM;
                $rowIncome->text = $request->get('text');
                $rowIncome->public_id = $publicId;
                $rowIncome->money_amount = $applicationIncome;

                if (isset($date)) {
                    $rowIncome->date = $date;
                }
                else {
                    $rowIncome->date = Carbon::today()->toDateString();
                }
                $rowIncome->save();
                DB::commit();
            }
            catch (QueryException $e) {
                report($e);
                DB::rollBack();

                return response([
                    'error' => true,
                    'errors' => array (
                        'main' => "Возникла ошибка при сохранении доходов, обратитесь к администратору"
                    )
                ], 400);
            }
        }
        //return response ($request, 400);

        if (is_numeric($applicationOutcome))
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
                if (isset($date)) {
                    $rowOutcome = Outcome::whereDate('date',$date)->first();
                }
                else {
                    $date = Carbon::today()->toDateString();
                    $rowOutcome = Outcome::whereDate('date', $date)->first();
                }

                if (!$rowOutcome) {
                    $rowOutcome = new Outcome();
                    $rowOutcome->date = $date;
                    $rowOutcome->activity_id = Activity::ID_INSTAGRAM;
                }

                $rowOutcome->money_amount = $applicationOutcome;
                $rowOutcome->save();
                DB::commit();
            }
            catch (QueryException $e) {
                report($e);
                DB::rollBack();
                return response([
                    'error' => true,
                    'errors' => array (
                        'main' => "Возникла ошибка при сохранении расходов, обратитесь к администратору"
                    )
                ], 400);
            }
        }

        $respArr = $this->getStatistics($date);
        $respArr['publics'] = $publics;

        if ($applicationIncome !== false) {
            $respArr['instagram_picture_income'] = $applicationIncome;
        }

        return response($respArr, 200);
    }

    public function seeStatistics($date)
    {
        $publics = IPublic::all();
        $respArr = $this->getStatistics($date);
        $respArr['publics'] = $publics;
        return response($respArr, 200);
    }

    public function deleteLast($date)
    {
        $ansInc = Income::latest('created_at')->first();
        $ansOut = Outcome::latest('created_at')->first();
        if (!$ansInc) {
            $ans = $ansOut->delete();
        }
        else if (!$ansOut) {
            $ans = $ansInc->delete();
        }
        else if ($ansInc->created_at > $ansOut->created_at) {
            $ans = $ansInc->delete();
        }
        else {
            $ans = $ansOut->delete();
        }
        if (!$ans) {
            return response([
                'error' => true,
                'errors' => array (
                    'main' => "Возникла ошибка при удалении последнего добавленного дохода/расхода"
                )
            ], 400);
        }
        $respArr = $this->getStatistics($date);
        return response($respArr, 200);
    }

    public function getStatistics($date)
    {
        $carbonDate = new Carbon($date);
        $income = array(
            'day'           => $this->getIncomeDay($date),
            'day_rabota'    => $this->getIncomeDay($date, IPublic::ID_RABOTA_VDK),
            'day_models'    => $this->getIncomeDay($date, IPublic::ID_MODELS_VDK),

            'month'         => $this->getIncomeMonth(true, $carbonDate),
            'month_rabota'  => $this->getIncomeMonth(true, $carbonDate, IPublic::ID_RABOTA_VDK),
            'month_models'  => $this->getIncomeMonth(true, $carbonDate, IPublic::ID_MODELS_VDK),

            'year'          => $this->getIncomeYear(true),
            'year_rabota'   => $this->getIncomeYear(true, IPublic::ID_RABOTA_VDK),
            'year_models'   => $this->getIncomeYear(true, IPublic::ID_MODELS_VDK)
        );

        $outcome = array(
            'day'           => $this->getOutcomeDay($date),
            'day_rabota'    => $this->getOutcomeDay($date, IPublic::ID_RABOTA_VDK),
            'day_models'    => $this->getOutcomeDay($date, IPublic::ID_MODELS_VDK),

            'month'         => $this->getOutcomeMonth($carbonDate),
            'month_rabota'  => $this->getOutcomeMonth($carbonDate,IPublic::ID_RABOTA_VDK),
            'month_models'  => $this->getOutcomeMonth($carbonDate,IPublic::ID_MODELS_VDK),

            'year'          => $this->getOutcomeYear(),
            'year_rabota'   => $this->getOutcomeYear(IPublic::ID_RABOTA_VDK),
            'year_models'   => $this->getOutcomeYear(IPublic::ID_MODELS_VDK)
        );


        $respArr = [
            'day_income'    => $income['day'],
            'day_outcome'   => $outcome['day'],
            'day_profit'    => $income['day'] - $outcome['day'],

            'day_income_rabota'     => $income['day_rabota'],
            'day_outcome_rabota'    => $outcome['day_rabota'],
            'day_profit_rabota'     => $income['day_rabota'] - $outcome['day_rabota'],

            'day_income_models'     => $income['day_models'],
            'day_outcome_models'    => $outcome['day_models'],
            'day_profit_models'     => $income['day_models'] - $outcome['day_models'],

            'month_income'      => $income['month']['summ'],
            'month_outcome'     => $outcome['month'],
            'month_profit'      => $income['month']['summ'] - $outcome['month'],
            'month_day_count'   => $income['month']['day_count'],

            'month_income_rabota'      => $income['month_rabota']['summ'],
            'month_outcome_rabota'     => $outcome['month_rabota'],
            'month_profit_rabota'      => $income['month_rabota']['summ'] - $outcome['month_rabota'],
            'month_day_count_rabota'   => $income['month_rabota']['day_count'],

            'month_income_models'      => $income['month_models']['summ'],
            'month_outcome_models'     => $outcome['month_models'],
            'month_profit_models'      => $income['month_models']['summ'] - $outcome['month_models'],
            'month_day_count_models'   => $income['month_models']['day_count'],

            'year_income'           => $income['year']['summ'],
            'year_outcome'          => $outcome['year'],
            'year_profit'           => $income['year']['summ'] - $outcome['year'],
            'year_day_count'        => $income['year']['day_count'],

            'year_income_rabota'      => $income['year_rabota']['summ'],
            'year_outcome_rabota'     => $outcome['year_rabota'],
            'year_profit_rabota'      => $income['year_rabota']['summ'] - $outcome['year_rabota'],
            'year_day_count_rabota'   => $income['year_rabota']['day_count'],

            'year_income_models'      => $income['year_models']['summ'],
            'year_outcome_models'     => $outcome['year_models'],
            'year_profit_models'      => $income['year_models']['summ'] - $outcome['year_models'],
            'year_day_count_models'   => $income['year_models']['day_count'],
        ];

        return $respArr;
    }

    public function destroy($id)
    {
        try {
            $income = Income::findOrFail($id);
            $income->delete();
        }
        catch (Exception $e) {
            return response(['mainError' => $e->getMessage()], 400);
        }
        return response([''], 200);
    }

    public function edit($id)
    {
        //
    }

    public function create()
    {
        //
    }

    private function getIncomeDay($date, $insta_public_id = null)
    {
        if (!$insta_public_id) {
            $rowsToday = Income::whereDate('date', $date)->get();
        }
        else if (is_numeric($insta_public_id)) {
            $rowsToday = Income::whereDate('date', $date)
                ->where('public_id', '=', $insta_public_id)
                ->get();
        }

        $todayIncome = 0;
        if (count($rowsToday) > 0) {
            foreach ($rowsToday as $incomeRow) {
                $todayIncome += $incomeRow->money_amount;
            }
        }

        return $todayIncome;
    }

    private function getOutcomeDay($date, $insta_public_id = null)
    {
        if (!$insta_public_id) {
            $rowsToday = Outcome::whereDate('date', $date)->get();
        }
        else if (is_numeric($insta_public_id)) {
            $rowsToday = Outcome::whereDate('date', $date)
                ->where('public_id', '=', $insta_public_id)
                ->get();
        }

        $todayOutcome = 0;
        if (count($rowsToday) > 0) {
            foreach ($rowsToday as $outcomeRow) {
                $todayOutcome += $outcomeRow->money_amount;
            }
        }

        return $todayOutcome;
    }

    private function getIncomeMonth($needToCountDays, Carbon $date, $insta_public_id = null)
    {
        if (!$insta_public_id) {
            $allRowsMonth = Income::whereDate('date', '>=',
                    $date->firstOfMonth()->toDateTimeString())
                ->whereDate('date', '<=',
                    $date->lastOfMonth()->toDateTimeString())
                ->get();
        }
        else if (is_numeric($insta_public_id)) {
            $allRowsMonth = Income
                ::whereDate('date', '>=', $date->firstOfMonth()->toDateTimeString())
                ->whereDate('date', '<=', $date->lastOfMonth()->toDateTimeString())
                ->where('public_id', '=', $insta_public_id)
                ->get();
        }

        $thisMonthIncome = 0;
        foreach($allRowsMonth as $incomeRow2){
            $thisMonthIncome += $incomeRow2->money_amount;
        }

        $result = ['summ' => $thisMonthIncome];

        if ($needToCountDays) {
            //$uniqDates = array_unique($dates);
            //$result['day_count'] = count($allRowsMonth);
            $gb = $allRowsMonth->groupBy('date');
            $result['day_count'] = count($gb);
        }
        return $result;
    }

    private function getOutcomeMonth(Carbon $date, $insta_public_id = null)
    {
        if (!$insta_public_id) {
            $monthOutcomeRows = Outcome
                ::whereDate('date', '>=', $date->firstOfMonth()->toDateTimeString())
                ->whereDate('date', '<=', $date->lastOfMonth()->toDateTimeString())
                ->get();
        }
        else if (is_numeric($insta_public_id)) {
            $monthOutcomeRows = Outcome
                ::whereDate('date', '>=', $date->firstOfMonth()->toDateTimeString())
                ->whereDate('date', '<=', $date->lastOfMonth()->toDateTimeString())
                ->where('public_id', '=', $insta_public_id)
                ->get();
        }
        $thisMonthOutcome = 0;

        if (count($monthOutcomeRows) > 0) {
            foreach($monthOutcomeRows as $outcomeRow2){
                $thisMonthOutcome += $outcomeRow2->money_amount;
            }
        }
        return $thisMonthOutcome;
    }

    private function getIncomeYear($needToCountDays, $insta_public_id = null)
    {
        if (!$insta_public_id) {
            $allRowsYear = Income
                ::whereDate('date', '>=', '2019-01-01')
                ->whereDate('date', '<=', Carbon::today())
                ->get();
        }
        else if (is_numeric($insta_public_id)) {
            $allRowsYear = Income
                ::whereDate('date', '>=', '2019-01-01')
                ->whereDate('date', '<=', Carbon::today())
                ->where('public_id', '=', $insta_public_id)
                ->get();
        }

        $thisYearIncome = 0;
        if (count($allRowsYear) > 0)
        foreach($allRowsYear as $incomeRow2) {
            $thisYearIncome += $incomeRow2->money_amount;
        }

        $result = ['summ' => $thisYearIncome];

        if ($needToCountDays) {
            $gb = $allRowsYear->groupBy('date');
            $result['day_count'] = count($gb);
        }
        return $result;
    }

    private function getOutcomeYear($insta_public_id = null)
    {
        if (!$insta_public_id) {
            $allRowsYear = Outcome
                ::whereDate('date', '>=', '2019-01-01')
                ->whereDate('date', '<=', Carbon::today())
                ->get();
        }
        else if (is_numeric($insta_public_id)) {
            $allRowsYear = Outcome
                ::whereDate('date', '>=', '2019-01-01')
                ->whereDate('date', '<=', Carbon::today())
                ->where('public_id', '=', $insta_public_id)
                ->get();
        }

        $thisYearOutcome = 0;
        if (count($allRowsYear) > 0) {
            foreach($allRowsYear as $outcomeRow2){
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