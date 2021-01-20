<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mockery\Exception;
use Propaganistas\LaravelPhone\PhoneNumber;
use Validator;

use App\Application as ApplicationModel;
use App\Worker as WorkerModel;
use App\WorkerPhone as WorkerPhoneModel;
use App\DebitCard as DebitCardModel;
use App\Exceptions\Application\assignWorker\TwoDiffWorkersException;


class Application extends Controller
{
    private function getMaskedPhone($phone)
    {
        $phoneHasNormalLength = (strlen($phone) >= 11);

        if ($phoneHasNormalLength) {
            $result = substr($phone, 0, 5) .
                '...' . substr($phone, strlen($phone) - 2, 2);
        }
        else {
            $result = $phone;
        }

        return $result;
    }

    private function getValidatedFormatPhone($phone)
    {
        $validator = Validator::make(['value' => $phone], ['value' => 'phone:RU']);
        if (!$validator->fails())
        {
            $phone = PhoneNumber::make($phone)->ofCountry('RU');
        }
        return $phone;
    }

    private function getMaskedDebitCard($card)
    {
        $strLen = strlen($card);
        $smallStr = ($strLen < 2);
        $isNotPhoneNotDebitCard = ($strLen >= 2 && $strLen < 11);
        $isPhone = ($strLen >= 11);
        $isDebitCard = ($strLen > 16);

        if ($smallStr)
        {
            $result = $card;
        }

        if ($isNotPhoneNotDebitCard)
        {
            $result = substr($card, 0, 1) .
                '...' . substr($card, strlen($card) - 1, 1);
        }

        if ($isPhone)
        {
            $result = substr($card, 0, 5) .
                '...' . substr($card, strlen($card) - 2, 2);
        }

        if ($isDebitCard)
        {
            $result = substr($card, 0, 4) .
                '...' . substr($card, strlen($card) - 4, 4);
        }

        return $result;
    }

    private function getMaskedAddress($addr)
    {
        return preg_replace('/\d+/u', '', $addr);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($date = 'today', $type = 'ordinary', $dispatcherId = null)
    {
        if ($date == 'all')
        {
            if ($type == 'account')
            {
                $apps = $dispatcherId ?
                    ApplicationModel
                        ::orderBy('date', 'desc')
                        ->where('pay_method', ApplicationModel::PM_ACCOUNT)
                        ->where('dispatcher_id', User::ID_DEVOCHKA)
                        ->get()
                    :
                    ApplicationModel
                        ::orderBy('date', 'desc')
                        ->where('pay_method', ApplicationModel::PM_ACCOUNT)
                        ->get();
            }
            else
            {
                $apps = $dispatcherId ?
                    ApplicationModel
                        ::orderBy('date', 'desc')
                        ->where('dispatcher_id', User::ID_DEVOCHKA)
                        ->get()
                    :
                    ApplicationModel
                        ::orderBy('date', 'desc')
                        ->get();
            }
        }
        else if ($date == 'actual') {
            $dateAfterWeek = Carbon::now()->addDays(7);

            $apps = $dispatcherId ?
                ApplicationModel
                    ::whereBetween(DB::raw('DATE(date)'), [$date, $dateAfterWeek])
                    ->orderBy('date', 'desc')
                    ->where('dispatcher_id', User::ID_DEVOCHKA)
                    ->get()
                :
                ApplicationModel
                    ::whereBetween(DB::raw('DATE(date)'),
                        [Carbon::today()->toDateString(), $dateAfterWeek->toDateString()])
                    ->orderBy('date', 'desc')
                    ->get();
        }
        else
        {
            if ($date == 'today')
            {
                $date = Carbon::today()->toDateString();
            }
            $apps = $dispatcherId ?
                ApplicationModel
                    ::orderBy('date', 'desc')
                    ->whereDate('date', $date)
                    ->where('dispatcher_id', User::ID_DEVOCHKA)
                    ->get()
                :
                ApplicationModel
                    ::orderBy('date', 'desc')
                    ->whereDate('date', $date)
                    ->get();
        }

        $rows = [];
        $appCount = count($apps);
        $shortLabels = ApplicationModel::getShortcutStateLabels('capital');

        if ($appCount > 0)
        {
            $childrens[0] = $apps[0];

            for ($i = 0, $j = 0; $i < $appCount; $i++)
            {
                $appRow = $apps[$i];
                $appRow->index = $i;
                $appRow->composedAppText = $this->composeAppText($appRow);

                $appRow->dispatcher = $appRow->getLable('dispatcher');
                $appRow->price = $appRow->getLable('price');
                $appRow->pay_method = $appRow->getLable('pay_method', $appRow->pay_method);
                $appRow->state_label = $shortLabels[$appRow->state];
                if ($appRow->state == ApplicationModel::ENDED_ST)
                {
                    $appRow->state_label .= ' ' . $appRow->getPayedByClientLabel();
                }
                $appRow->edg = $appRow->getLable('edg', $appRow->edg);

                if (!User::can('WatchClientsPersonalData'))
                {
                    $phone = $appRow->client_phone_number;
                    $appRow->client_phone_number = $this->getMaskedPhone($phone);

                    $appRow->address = $this->getMaskedAddress($appRow->address);
                }

                if ($i > 0)
                {
                    if ($childrens[$j]->date == $apps[$i]->date)
                    {
                        $childrens[] = $appRow;
                        $j++;
                    }
                    else
                    {
                        $rows[] = [
                            'mode' => 'span',
                            'label' => $childrens[$j]->date,
                            'html' => false,
                            'children' => $childrens
                        ];
                        $childrens = [];
                        $j = 0;
                        $childrens[$j] = $appRow;
                    }
                }
            }
            $rows[] = [
                'mode' => 'span',
                'label' => $childrens[$j]->date,
                'html' => false,
                'children' => $childrens
            ];
        }

        return [
            'userPrivileges'=> User::getPrivileges(),
            'apps'          => $apps,
            'rows'          => $rows,
            'date'          => $date,
            'appCount'      => $appCount,
            'state_colors'  => ApplicationModel::getStateColors(),
        ];
    }

    public function end(Request $request)
    {
        try
        {
            $appId = $request->get('app_id');
            $workHours = $request->get('work_hours');

            $application = ApplicationModel::find($appId);

            if (empty($workHours))
            {
                $startTime = Carbon::parse($application->time);
                $finishTime = Carbon::now();
                $workHours = ($finishTime->diffInSeconds($startTime) - 5*60) / (60 * 60);
                if ($workHours - floor($workHours) <= 0.25)
                {
                    $workHours = floor($workHours);
                }
                else
                {
                    $workHours = floor($workHours) + 1;
                }
                if ($workHours < 2)
                {
                    $workHours = 2;
                }
            }

            $application->setWorkerHours($workHours);

            foreach ($application->getPivotData() as $pd)
            {
                if (ApplicationModel::isThisChildWorker($pd))
                {
                    if (WorkerModel::ifRelationWithParentIsPlus($pd))
                    {
                        $application->setParentPlusWorkerData($pd);
                    }
                    else if (WorkerModel::ifRelationWithParentIsInstead($pd))
                    {
                        $application->setParentInsteadWorkerData($pd);
                    }
                }
                if (
                    ! ApplicationModel::isThisParentWorker($pd) || (
                        ApplicationModel::isThisParentWorker($pd) &&
                        WorkerModel::ifRelationWithParentIsPlus($pd)
                    ))
                {
                    $application->addWorker($pd);
                }
            }

            /*return response([
                'parentWorkers' => $application->getParentWorkers()
            ], 400);*/

            $application->updatePivotAllWorkersHoursAndMoney();
            $application->saveTotalWorkHoursAndMoney($workHours);

            return response([], 200);
        }
        catch(\Exception $e)
        {
            return response([
                'result' => 0,
                'errors' => [
                    'operations' => 'Ошибка ! ' . $e->getMessage()
                ]
            ], 400);
        }
    }

    public function readyToPay($id)
    {
        try {
            $app = ApplicationModel::find($id);
            $app->state = ApplicationModel::READY_TO_PAY_ST;
            $app->save();
        }
        catch (Exception $e) {
            return response([
                'errors' => [
                    'operations' => 'Ошибка ! ' . $e->getMessage()
                ]
            ], 400);
        }
        return response([], 200);
    }

    public function rollback($id)
    {
        try {
            $app = ApplicationModel::find($id);
            $app->state = ApplicationModel::CLOSED_ST;
            $app->save();
        }
        catch (Exception $e) {
            return response([
                'errors' => [
                    'operations' => 'Ошибка ! ' . $e->getMessage()
                ]
            ], 400);
        }
        return response([], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * address: "Каплунова 2"
     * date: "2019-12-18"
     * time: "23:23"
     * worker_count: 2
     * price: "300"
     * price_per_hour: 1
     * text: "хуярить"
     * pay_method: 1
     * client_phone_number: "+7 234 324-34-34"
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $app = new ApplicationModel();
        $app->address               = $request->get('address');
        $app->date                  = $request->get('date');
        $app->time                  = $request->get('time');
        $app->worker_count          = 0;
        $app->worker_total          = $request->get('worker_total');
        $app->price                 = $request->get('price');
        $app->price_for_worker      = $request->get('price_for_worker');
        $app->hourly_job            = $request->get('hourly_job');
        $app->what_to_do            = $request->get('what_to_do');
        $app->edg                   = $request->get('edg');
        $app->pay_method            = $request->get('pay_method');
        $app->client_phone_number   = $request->get('client_phone_number');
        $app->dispatcher_id         = $request->get('dispatcher_id');
        $app->payed_by_client = 0;
        $app->state = 1; // открыта
        $app->outcome = 0;
        $app->profit = 0;
        $app->save();

        return response($app);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $application = ApplicationModel::find($id);
        $application->composedAppText = $this->composeAppText($application, "<br />", " ");
        $application->appTextCopyToCb = $this->composeAppText($application, "\n");
        $workers = $application->workers;
        //return response([is_a($workers, 'Illuminate\Database\Eloquent\Collection')], 400);

        $application->worker_count = count($workers);

        if (!User::can('WatchClientsPersonalData'))
        {
            $formattedPhone = $this->getValidatedFormatPhone($application->client_phone_number);
            $application->client_phone_number = $this->getMaskedPhone($formattedPhone);
            $application->address = $this->getMaskedAddress($application->address);
        }

        $appWorkerPivotData = $application->getPivotData();

        $index = 0;
        $childIndexOf = [];
        $notChildWorkers = [];
        $childWorkers = [];

        foreach ($workers as $worker) {
            $worker->tie_debit_card = false;

            foreach ($appWorkerPivotData as $aw)
            {
                if ($worker->id == $aw->worker_id)
                {
                    if ($application->state >= ApplicationModel::ENDED_ST)
                    {
                        $worker->parent_worker_id = $aw->parent_worker_id;
                        $worker->work_hours = $aw->work_hours;
                        $worker->money = $aw->worker_money;
                        $worker->got_money = $aw->worker_got_money;
                        $worker->child = 0;
                        $worker->parent = 0;

                        $dbDebitCard = DebitCardModel::find($aw->debit_card_id);
                        $worker->debit_card = $dbDebitCard->number;
                        $worker->responsible_for_money = $aw->responsible_for_money;
                        $worker->total_parent_work_hours = null;
                        $worker->total_parent_money = null;
                        $worker->relation_type = null;

                        if ($worker->responsible_for_money === null) {
                            $worker->responsible_for_money = 0;
                        }

                        if (ApplicationModel::isThisParentWorker($aw)) {
                            $worker->total_parent_work_hours = $aw->total_parent_work_hours;
                            $worker->total_parent_money = $aw->total_parent_money;
                            $worker->relation_type = $aw->relation_type;
                            $worker->parent = 1;
                            if ($worker->relation_type == 'i') {
                                $application->worker_count -= 1;
                            }
                        }
                        if (ApplicationModel::isThisChildWorker($aw)) {
                            $worker->child = 1;
                            //$worker->debit_card = '';
                            if (!array_key_exists($worker->parent_worker_id, $childWorkers)) {
                                $childWorkers[$worker->parent_worker_id] = [];
                                $childIndexOf[$worker->parent_worker_id] = 0;
                            }
                            array_push($childWorkers[$worker->parent_worker_id], $worker);
                        }
                    }
                    else
                    {
                        $worker->parent = 0;
                        $worker->child = 0;
                        $worker->parent_worker_id = $aw->parent_worker_id;
                        $worker->total_parent_money = null;
                        $worker->total_parent_work_hours = null;

                        if (ApplicationModel::isThisParentWorker($aw)) {
                            $worker->total_parent_work_hours = $aw->total_parent_work_hours;
                            $worker->total_parent_money = $aw->total_parent_money;
                            $worker->relation_type = $aw->relation_type;
                            $worker->parent = 1;
                            if ($worker->relation_type == 'i') {
                                $application->worker_count -= 1;
                            }
                        }
                        if (ApplicationModel::isThisChildWorker($aw)) {
                            $worker->child = 1;
                           // $worker->debit_card = '';
                            if (!array_key_exists($worker->parent_worker_id, $childWorkers)) {
                                $childWorkers[$worker->parent_worker_id] = [];
                                $childIndexOf[$worker->parent_worker_id] = 0;
                            }
                            array_push($childWorkers[$worker->parent_worker_id], $worker);
                        }

                        $dbDebitCard = DebitCardModel::find($aw->debit_card_id);
                        if ($dbDebitCard !== null) {
                            $worker->debit_card = $dbDebitCard->number;
                        }
                        else {
                            $worker->debit_card = '';
                        }
                        $worker->responsible_for_money = $aw->responsible_for_money;
                        if ($worker->responsible_for_money === null) {
                            $worker->responsible_for_money = 0;
                        }
                    }
                    $worker->created_at = $aw->created_at;
                    $worker->updated_at = $aw->updated_at;

                    if (!User::can('WatchWorkersPersonalData'))
                    {
                        $worker->debit_card = $this->getMaskedDebitCard($worker->debit_card);
                    }
                }
            }
            $p = $worker->phones;
            $c = $worker->debitCards;
            $c = $c->unique('number');

            $phonesCount = count($p);

            if ($phonesCount == 0)
            {
                $worker->phone = $this->getValidatedFormatPhone($worker->name);
                if (!User::can('WatchWorkersPersonalData'))
                {
                    $worker->phone = $this->getMaskedPhone($worker->phone);
                }
            }
            if ($phonesCount == 1)
            {
                $worker->phone = $this->getValidatedFormatPhone($p[0]->number);
                if (!User::can('WatchWorkersPersonalData'))
                {
                    $worker->phone = $this->getMaskedPhone($worker->phone);
                }
            }
            else if ($phonesCount >= 2)
            {
                $p0 = $this->getValidatedFormatPhone($p[0]->number);
                if (!User::can('WatchWorkersPersonalData'))
                {
                    $p0 = $this->getMaskedPhone($p0);
                }

                $p1 = $this->getValidatedFormatPhone($p[1]->number);
                if (!User::can('WatchWorkersPersonalData'))
                {
                    $p1 = $this->getMaskedPhone($p1);
                }

                if (strpos($p[0]->type, 'c') !== false)
                {
                    $worker->phone = $p0 . " (" . $p1 . ')';
                }
                else
                {
                    $worker->phone = $p1 . " (" . $p0 . ')';
                }
            }

            if ($worker->child)
            {
                $worker->index = $childIndexOf[$worker->parent_worker_id]++;
            }
            else
            {
                $worker->index = $index++;
                array_push($notChildWorkers, $worker);
            }
        }

        $stateLabels = ApplicationModel::getStateLabels('lower');
        $stateLabels[\App\Application::ENDED_ST] .= ' ' . $application->getPayedByClientLabel();

        return response([
            'userPrivileges'     => User::getPrivileges(),
            'application'        => $application,
            'workers'            => $notChildWorkers,
            'child_workers'      => $childWorkers,
            'child_workers_copy' => $childWorkers,
            'state_labels'       => $stateLabels,
            'state_colors'       => ApplicationModel::getStateColors(),
            'CLOSED_ST'          => ApplicationModel::CLOSED_ST,
            'ENDED_ST'           => ApplicationModel::ENDED_ST,
            'READY_TO_PAY_ST'    => ApplicationModel::READY_TO_PAY_ST,
            'PAYED_ST'           => ApplicationModel::PAYED_ST,
            'NOT_PAYED_ST'       => ApplicationModel::NOT_PAYED_ST,
            'PM_CASH'            => ApplicationModel::PM_CASH,
            'PM_ACCOUNT'         => ApplicationModel::PM_ACCOUNT
        ], 200);
    }

    public function workerGotMoney(Request $request)
    {
        try
        {
            $appId = $request->get('app_id');
            $workerId = $request->get('worker_id');
            $isParent = $request->get('parent');

            $application = ApplicationModel::find($appId);
            $application->updatePivot($workerId, ['worker_got_money' => 1]);
            if ($isParent)
            {
                $application->updatePivotsByParent($workerId, ['worker_got_money' => 1]);
            }
            $pivotData = $application->getPivotData();

            $allWorkersGotMoney = true;
            foreach ($pivotData as $pd) {
                if ($pd->worker_got_money == 0) {
                    $allWorkersGotMoney = false;
                }
            }

            if ($allWorkersGotMoney) {
                if ($application->payed_by_client) {
                    $application->state = ApplicationModel::PAYED_ST;
                    $application->save();
                    $application->addToReport();
                }
                else {
                    $application->state = ApplicationModel::NOT_PAYED_ST;
                    $application->save();
                }
            }
        }
        catch (Exception $e) {
            return response([
                'errors' => array(
                    'worker_got_money' => 'Ошибка #4! Не получилось сохранить инфу об оплате'
                )
            ], 400);
        }
        return response([
            'allWorkersGotMoney' => $allWorkersGotMoney
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function saveData(Request $request)
    {
        $appId = $request->get('application_id');
        $workers = $request->get('workers');
        $childWorkers = $request->get('child_workers');
        $originalWorkersCopy = $request->get('original_workers_copy');
        $originalChildWorkersCopy = $request->get('original_child_workers_copy');
        $applicationIncome = $request->get('application_income');

      /* return response([
            $workers,
            $originalWorkersCopy
        ], 400);*/

        $application = ApplicationModel::find($appId);

        if ($applicationIncome <= 0 && $application->state == ApplicationModel::ENDED_ST) {
            return response([
                'errors' => array(
                    'operations' => 'Ошибка #8! Укажите сколько пришло от клиента, должно быть больше 0!'
                )
            ], 400);
        }

        $totalMoney = 0;
        $totalWorkHours = 0;
        $updatedParentWorkers = [];
        foreach ($workers as $i => $worker)
        {
            if (key_exists('money', $worker))
            {
                if ($worker['parent'])
                {
                    $totalMoney += $worker['total_parent_money'];
                }
                else
                {
                    $totalMoney += $worker['money'];
                }
            }
            if (key_exists('work_hours', $worker)) {
                $totalWorkHours += $worker['work_hours'];
            }

            if (trim($worker['phone']) != trim($originalWorkersCopy[$i]['phone']))
            {
                $dbWp = WorkerPhoneModel::where('number', $originalWorkersCopy[$i]['phone'])->first();
                $dbWp->number = $worker['phone'];
                $dbWp->save();
            }

            // это значит, что в поле карты стояла ничейная карта, а теперь
            // диспетчер, хочет, чтобы данная карта стала привязанной к данному рабочему
            // это значит, что если к этому рабочему была привязана какая-то другая карта
            // то она, будет отвязана от него, но продолжит существовать в базе
            if (($worker['tie_debit_card'] != $originalWorkersCopy[$i]['tie_debit_card']) &&
                (trim($worker['debit_card']) == trim($originalWorkersCopy[$i]['debit_card'])))
            {
                /*return response([
                    "worker['tie_debit_card'] != originalWorkersCopy[i]['tie_debit_card']"
                ], 400);*/
                $dbDc  = DebitCardModel::where('number', $worker['debit_card'])->first();
                $dbDc->updateWorkerCardRelation($worker);
            }

            if (trim($worker['debit_card']) != trim($originalWorkersCopy[$i]['debit_card']))
            {

                $dbDc = DebitCardModel::where('number', $worker['debit_card'])->first();

                if ($dbDc !== null)
                {
                    $isItAnothersWorkerCard = false;
                    foreach ($dbDc->workers as $w) {
                        if ($worker['id'] != $w->id) {
                            $isItAnothersWorkerCard = true;
                            break;
                        }
                    }
                    //return response([$isItAnothersWorkerCard && $dbDc->isNotEmpty()], 400);

                    if ($isItAnothersWorkerCard && $dbDc->isNotEmpty())
                    {
                        if ($worker['tie_debit_card'])
                        {
                            return response([
                                'errors' => array(
                                    'operations' => 'Ошибка #7! Нельзя привязать чужую карту!'
                                )
                            ], 400);
                        }
                        $application->updatePivot($worker['id'], [
                            'debit_card_id' => $dbDc->id,
                            'updated_at' => Carbon::now()
                        ]);
                    }
                    else
                    {
                        // ничейная карточка
                        if ($dbDc->workers->isEmpty())
                        {
                            if ($worker['tie_debit_card'])
                            {
                                $dbDc->updateWorkerCardRelation($worker);
                            }
                            $application->updatePivot($worker['id'], [
                                'debit_card_id' => $dbDc->id,
                                'updated_at' => Carbon::now()
                            ]);
                        }
                        // если это карточка данного человека,
                        // либо это пустая карточка
                        else
                        {
                            if ($worker['tie_debit_card']) {
                                $dbDc->updateWorkerCardRelation($worker);
                            }

                            $application->updatePivot($worker['id'], [
                                'debit_card_id' => $dbDc->id,
                                'updated_at' => Carbon::now()
                            ]);
                        }
                    }
                }
                else
                {
                    /*return response([
                        "dbDc == null"
                    ], 400);*/
                    if ($worker['tie_debit_card'])
                    {
                        $w = WorkerModel::where('id', $worker['id'])->first();

                        $latestDcWorker = $w->getLatestDebitCardRelation();

                        if (!$latestDcWorker)
                        {
                            $debitCard = new DebitCardModel();
                            $debitCard->number = $worker['debit_card'];
                            $debitCard->save();
                            $debitCard->insertWorkerCardRelation($worker);
                        }
                        else
                        {
                            $debitCard = DebitCardModel::find($latestDcWorker->debit_card_id);
                            $debitCard->number = $worker['debit_card'];
                            $debitCard->save();
                        }

                        $application->updatePivot($worker['id'], [
                            'debit_card_id' => $debitCard->id
                        ]);
                    }
                    // карта новая и ее не нужно привязывать
                    else
                    {
                        $debitCard = new DebitCardModel();
                        $debitCard->number = $worker['debit_card'];
                        $debitCard->save();

                        $application->updatePivot($worker['id'], [
                            'debit_card_id' => $debitCard->id
                        ]);
                    }
                }
            }

            if (
                (
                    ($worker['total_parent_money'] !== null) &&
                    ($worker['total_parent_work_hours'] !== null)
                ) &&
                (
                    (
                        trim($worker['total_parent_money'])      !=
                        trim($originalWorkersCopy[$i]['total_parent_money'])
                    ) ||
                    (
                        trim($worker['total_parent_work_hours']) !=
                        trim($originalWorkersCopy[$i]['total_parent_work_hours'])
                    )
                )
            )
            {
                $application->updatePivot($worker['id'], [
                    'total_parent_work_hours' => $worker['total_parent_work_hours'],
                    'total_parent_money' => $worker['total_parent_money']
                ]);
            }

            if  ((key_exists('money', $worker) || (key_exists('work_hours', $worker))) &&
                ((trim($worker['money'])       != trim($originalWorkersCopy[$i]['money'])) ||
                (trim($worker['work_hours'])  != trim($originalWorkersCopy[$i]['work_hours']))))
            {
                $application->updatePivot($worker['id'], [
                    'worker_money' => $worker['money'],
                    'work_hours' => $worker['work_hours']
                ]);
            }

            if ((key_exists('responsible_for_money', $worker) &&
                key_exists('responsible_for_money', $originalWorkersCopy[$i])) &&
               ($worker['responsible_for_money'] != $originalWorkersCopy[$i]['responsible_for_money']))
            {
                $application->updatePivot($worker['id'], [
                    'responsible_for_money' => $worker['responsible_for_money']
                ]);
            }

            if ($worker['parent'])
            {
                foreach ($childWorkers[$worker['id']] as $j => $childWorker)
                {
                    if (trim($childWorker['debit_card']) !=
                        trim($originalChildWorkersCopy[$worker['id']][$j]['debit_card']))
                    {
                        $dbDc = DebitCardModel::where('number', $childWorker['debit_card'])->first();
                        if ($dbDc !== null)
                        {
                            $application->updatePivot($childWorker['id'], [
                                'debit_card_id' => $dbDc->id,
                                'updated_at' => Carbon::now()
                            ]);
                        }
                        else
                        {
                            $debitCard = new DebitCardModel();
                            $debitCard->number = $childWorker['debit_card'];
                            $debitCard->save();

                            $application->updatePivot($childWorker['id'], [
                                'debit_card_id' => $debitCard->id
                            ]);
                        }
                    }

                    if (trim($childWorker['phone']) !=
                        trim($originalChildWorkersCopy[$worker['id']][$j]['phone']))
                    {
                        $w = WorkerModel::find($childWorker['id']);
                        $w->updateVal([
                            'name' => $childWorker['phone']
                        ]);
                    }

                    if (array_key_exists('money',$childWorker) &&
                        (
                            (
                                trim($childWorker['money']) !=
                                trim($originalChildWorkersCopy[$worker['id']][$j]['money'])
                            )
                        ||
                            (
                                trim($childWorker['work_hours']) !=
                                trim($originalChildWorkersCopy[$worker['id']][$j]['work_hours'])
                            )
                        )
                    )
                    {
                        $application->updatePivot($childWorker['id'], [
                            'worker_money' => $childWorker['money'],
                            'work_hours'   => $childWorker['work_hours']
                        ]);
                    }
                }
            }
        }

        $application->income = $applicationIncome;
        $application->outcome = $totalMoney;
        $application->profit = $application->income - $application->outcome;
        $application->total_work_hours = $totalWorkHours;
        $application->save();

        return response([], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $app = ApplicationModel::find($id);

        return [
            'application' => $app
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $app = ApplicationModel::find($id);

        $app->address               = $request->get('address');
        $app->date                  = $request->get('date');
        $app->time                  = str_replace('_', '', $request->get('time'));
        $app->worker_total          = $request->get('worker_total');
        $app->price                 = $request->get('price');
        $app->price_for_worker      = $request->get('price_for_worker');
        $app->hourly_job            = $request->get('hourly_job');
        $app->what_to_do            = $request->get('what_to_do');
        $app->edg                   = $request->get('edg');
        $app->pay_method            = $request->get('pay_method');
        $app->client_phone_number   = $request->get('client_phone_number');
        $app->dispatcher_id         = $request->get('dispatcher_id');

        $app->worker_count = $app->getWorkerCount();
        $app->changeState();

        $app->save();

        return response ([], 200);
    }

    // номер последней ошибки #5
    public function assignWorker(Request $request)
    {

        /*return response([
            'application_id' => $request->get('application_id'),
            'phone_whatsapp' => $request->get('phone_whatsapp'),
            'phone_call' => $request->get('phone_call'),
            'different_phone_call' => $request->get('different_phone_call'),
            'debit_cards' => $request->get('debit_cards'),
            'plus_workers' => $request->get('plus_workers'),
            'instead_workers' => $request->get('instead_workers')
        ], 400);*/

        $phoneWhatsapp      = $request->get('phone_whatsapp');
        $phoneCall          = $request->get('phone_call');
        $applicationId      = $request->get('application_id');
        $plus_workers       = $request->get('plus_workers');
        $instead_workers    = $request->get('instead_workers');
        $debitCards         = $request->get('debit_cards');

        //[[{"phone":null,"card_index":1},{"phone":null,"card_index":1}]]

        $application = ApplicationModel::find($applicationId);
        $workerCount = $application->getWorkerCount();

        if (($application->worker_total - $workerCount < 1 + count($plus_workers)) ||
            ($application->worker_total - $workerCount < count($instead_workers)))
        {
            if (!is_null($phoneWhatsapp) || !is_null($phoneCall)) {
                return response(['Нельзя больше назначать рабочих!!!'], 400);
            }
        }

        if (is_null($debitCards[0]['number']))
        {
            return response([
                'error' => true,
                'errors' => array(
                    'debit_cards' => 'Ошибка #1 ! Карта 1 - обязательное поле, вы должны' .
                        ' указать хоть одну карту рабочего!'
                )
            ], 400);
        }

        if (is_null($phoneWhatsapp)) {
            if (is_null($phoneCall)) {
                return response([
                    'error' => true,
                    'errors' => array(
                        'assign_worker_phone' => 'Ошибка #2! Нужен хоть один номер телефона!'
                    )
                ], 400);
            }
        }
        else {
            $phoneWhatsapp = PhoneNumber::make($phoneWhatsapp);
            $request->validate(
                ['phone_whatsapp'     => 'phone:RU'],
                ['phone_whatsapp.phone' => 'Неправильный номер телефона в Whatsapp']
            );

            $dbPhoneWhatsapp = WorkerPhoneModel::firstOrNew(['number' => $phoneWhatsapp],
                [
                    'type' => 'cw',
                    'worker_id' => 0
                ]);
            //$dbPhoneWhatsapp->save();
        }

        if (!is_null($phoneCall)) {
            if (!is_null($phoneWhatsapp)) {
                $dbPhoneWhatsapp->type = 'w';

                if ($phoneCall == $phoneWhatsapp) {
                    $phoneCall = null;
                }
            }

            $phoneCall = PhoneNumber::make($phoneCall)->ofCountry('RU');
            $request->validate(
                ['phone_call' => 'phone:RU'],
                ['phone_call.phone' =>
                    'Неправильный номер телефона для звонка, либо он не является российским']
            );

            $dbPhoneCall = WorkerPhoneModel::firstOrNew(['number' => $phoneCall],
                [
                    'type' => 'c',
                    'worker_id' => 0
                ]);

            if (!is_null($phoneWhatsapp)) {

                if ($dbPhoneWhatsapp->worker_id != 0) {

                    if ($dbPhoneCall->worker_id != 0) {

                        if ($dbPhoneCall->worker_id != $dbPhoneWhatsapp->worker_id) {
                            $e = new TwoDiffWorkersException();
                            $e->setWhatsappPhoneAndWorkerById($phoneWhatsapp,
                                $dbPhoneWhatsapp->worker_id);
                            $e->setCallPhoneAndWorkerById($phoneCall,
                                $dbPhoneCall->worker_id);
                            throw $e;
                        }
                        //else оба worker_id совпадают и не равны 0
                    }
                    else {
                        $dbPhoneCall->worker_id = $dbPhoneWhatsapp->worker_id;
                        $dbPhoneCall->type = 'cw';
                    }

                    $dbPhoneCall->save();
                    $worker = WorkerModel::find($dbPhoneCall->worker_id);
                }
                else {
                    if ($dbPhoneCall->worker_id != 0) {
                        $dbPhoneWhatsapp->worker_id = $dbPhoneCall->worker_id;

                        $worker = WorkerModel::find($dbPhoneCall->worker_id);
                    }
                    else { // оба номера - новые, но принадлежат одному рабочему
                        $worker = new WorkerModel();
                        $worker->name = $phoneWhatsapp;
                        $worker->save();

                        $dbPhoneCall->worker_id = $worker->id;
                        $dbPhoneCall->save();

                        $dbPhoneWhatsapp->worker_id = $worker->id;
                    }
                }

                $dbPhoneWhatsapp->save();
            }
            else {
                if ($dbPhoneCall->worker_id != 0) {
                    $worker = WorkerModel::find($dbPhoneCall->worker_id);
                    $dbPhoneCall->type = 'c';
                    $dbPhoneCall->save();
                }
                else {
                    $worker = new WorkerModel();
                    $worker->name = $phoneCall;
                    $worker->save();

                    $dbPhoneCall->worker_id = $worker->id;
                    $dbPhoneCall->save();
                }
            }
        }
        else {
            if ($dbPhoneWhatsapp->worker_id != 0) {
                $worker = WorkerModel::find($dbPhoneWhatsapp->worker_id);
            }
            else {
                $worker = new WorkerModel();
                $worker->name = $phoneWhatsapp;
                $worker->save();

                $dbPhoneWhatsapp->worker_id = $worker->id;
                $dbPhoneWhatsapp->save();
            }
        }

        $debitCard = DebitCardModel::where('number', $debitCards[0]['number'])
                        ->latest()
                        ->first();

        if (!$debitCard) {
            $latestDcWorker = $worker->getLatestDebitCardRelation();

            if (!$latestDcWorker) {
                $debitCard = new DebitCardModel();
                $debitCard->number = $debitCards[0]['number'];
                $debitCard->save();

                if ($debitCards[0]['tie'] == true) {
                    $debitCard->insertWorkerCardRelation($worker);
                }
            }
            else if ($debitCards[0]['tie'] == true) {
                $latestDC = DebitCardModel::find($latestDcWorker->debit_card_id);
                //return response([$debitCard],400);
                if ($latestDC->isNotEmpty()) {
                    $latestDC->number = $debitCards[0]['number'];
                    $latestDC->save();
                    $debitCard = $latestDC;
                    //DebitCardModel::updateNumber($debitCard->id, $debitCards[0]['number']);
                }
                else {
                    $debitCard = new DebitCardModel();
                    $debitCard->number = $debitCards[0]['number'];
                    $debitCard->save();
                    $debitCard->updateWorkerCardRelation($worker);
                }
            }
            else {
                $debitCard = new DebitCardModel();
                $debitCard->number = $debitCards[0]['number'];
                $debitCard->save();
            }

            $application->updatePivot($worker['id'], [
                'debit_card_id' => $debitCard->id
            ]);
        }
        else if ($debitCards[0]['tie'] == true) {
            //return response([ $debitCard->anyWorkerCardRelationExists()],400);
            if ( WorkerModel::anyCardRelationExists($worker)) {
                $debitCard->updateWorkerCardRelation($worker);
            }
            else {
                $debitCard->insertWorkerCardRelation($worker);
            }
        }

        $workers = [];
        $typeOfWorkers = '';
        if (count($plus_workers) > 0) {
            $workers = $plus_workers;
            $typeOfWorkers = ApplicationModel::RT_PLUS;
        }
        else if (count($instead_workers) > 0) {
            $workers = $instead_workers;
            $typeOfWorkers = ApplicationModel::RT_INSTEAD;
        }

        $row = [
            'debit_card_id' => $debitCard->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];
        if ($typeOfWorkers != '') {
            $row['relation_type'] = $typeOfWorkers;
        }
        $application->workers()->syncWithoutDetaching([$worker->id => $row]);

        $attachWorkers = [];
        foreach ($workers as $index => $w) {

            if (is_null($w['phone'])) {
                $wName = $typeOfWorkers . ($index+1);
            }
            else {
                $wName = $w['phone'];
            }

            $newWorker = new WorkerModel([
                'name' => $wName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $newWorker->save();

            $attachWorkers[$newWorker->id] = [
                'debit_card_id' => $debitCard->id,
                'parent_worker_id' => $worker->id,
                'relation_type' => $typeOfWorkers,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        $application->workers()->sync($attachWorkers, false);
        $application->saveWorkerCount();
        $application->changeState();

        return response([], 200);
    }

    public function deleteWorker(Request $request)
    {
        try {
            $childWorkers = $request->get('child_workers');
            $parentWorkerId = $request->get('parent_worker_id');
            $appId = $request->get('application_id');
            $workerId = $request->get('worker_id');

            $app = ApplicationModel::find($appId);

            if ($app->state >= ApplicationModel::ENDED_ST) {
                return response([
                    'error' => true,
                    'errors' => array(
                        'delete' => 'Ошибка #3 ! Нельзя удалить рабочего' .
                            ' когда заявка была завершена!'
                    )
                ], 400);
            }
            $app->workers()->detach($workerId);
            $app->saveWorkerCount();
            $app->changeState();

            if ($parentWorkerId !== null) {
                if (array_key_exists($parentWorkerId, $childWorkers) && count($childWorkers[$parentWorkerId]) == 1) {
                    $app->removeParentRelation($parentWorkerId);
                }
            }
            else {
                if (array_key_exists($workerId, $childWorkers)) {
                    $app->deleteChildWorkers($workerId);
                    $app->removeParentRelation($workerId);
                }
            }
        }
        catch (Exception $e) {
            return response([$e->getMessage()], 400);
        }

        return response([], 200);
    }

    protected function doesExistWorkerWithPhone ($workerPhone) {
        return (count($workerPhone) == 1);
    }

    public function payedByClient(Request $request)
    {
        try {
            $appId = $request->get('id');
            $payed = $request->get('payed_by_client');
            $income = $request->get('income');

            $app = ApplicationModel::find($appId);

            $app->income = $income;
            $app->profit = $app->income - $app->outcome;
            $app->payed_by_client = $payed;

            if ($app->state == ApplicationModel::PAYED_ST) {
                $app->state = ApplicationModel::NOT_PAYED_ST;
            }
            else if ($app->state == ApplicationModel::NOT_PAYED_ST) {
                $app->state = ApplicationModel::PAYED_ST;
                $app->addToReport();
            }
            $app->save();
        }
        catch (Exception $e) {
            return response([
                'errors' => [
                    'operations' => 'Ошибка ! ' . $e->getMessage()
                ]
            ], 400);
        }
        return response([],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $app = ApplicationModel::findOrFail($id);
            $app->delete();
            return [
                'result' => 1
            ];
        }
        catch (\Exception $e) {
            return [
                'result' => 0,
                'error_msg' => $e->getMessage()
            ];
        }

    }

    protected function composeAppText(ApplicationModel $m, $separator = "\n", $firstChar = '')
    {
        //return [\DateTime::createFromFormat('Y-m-d', $m->date)];
        if (\DateTime::createFromFormat('Y-m-d', $m->date) !== FALSE) {
            if (Carbon::today() == Carbon::parse($m->date)) {
                $timePrefix = '';
            } else if (Carbon::today() == Carbon::parse($m->date)->subDay()) {
                $timePrefix = 'Завтра ';
            } else {
                $timePrefix = $m->date . ' ';
            }
        }
        else {
            $timePrefix = '';
        }

        $payMethod = '';
        if ($m->pay_method == ApplicationModel::PM_CARD) {
            $payMethod = 'ВЗЯЛИ ЗАЯВКУ - СКИНЬТЕ КАРТУ' . $separator . 'Оплата на карту';
        }
        else if ($m->pay_method == ApplicationModel::PM_CASH) {
            $payMethod = 'Оплата наличка';
        }

        if ($m->hourly_job) {
            $priceStr = $m->price_for_worker . " рчас";
        }
        else {
            $priceStr = $m->price_for_worker . ' р за 8 часов';
        }

        if (!User::can('WatchClientsPersonalData'))
        {
            $m->address = $this->getMaskedAddress($m->address);
        }

        return  $firstChar . $m->address . $separator .
                $timePrefix . $m->time . $separator .
                $m->worker_total . " чел " . $separator .
                $priceStr .$separator .
                $m->what_to_do . $separator .
                $payMethod;
    }
}
