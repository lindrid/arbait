<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\DB;
use function PHPSTORM_META\type;

use App\Activity;

class Application extends Model
{
    protected $table = "applications";

    // application states
    public const OPENED_ST          = 1;
    public const CLOSED_ST          = 2;
    public const ENDED_ST           = 3;
    public const READY_TO_PAY_ST    = 4;
    public const NOT_PAYED_ST       = 5;
    public const PAYED_ST           = 6;
    public const UNCONFIRMED_ST     = 0;

    public const WORKDAY_HOURS      = 8;

    public const PM_CARD    = 1;
    public const PM_CASH    = 2;
    public const PM_ACCOUNT = 3;

    public const RT_PLUS    = '+';
    public const RT_INSTEAD = 'i';

    private static $stateLabels = [
        self::OPENED_ST       => 'Открыта',
        self::CLOSED_ST       => 'Закрыта',
        self::ENDED_ST        => 'Завершена',
        self::READY_TO_PAY_ST => 'Готова к оплате',
        self::NOT_PAYED_ST    => 'Не оплачена',
        self::PAYED_ST        => 'Оплачена',
        self::UNCONFIRMED_ST  => 'Не подтверждена'
    ];
    private static $lowerStateLabels = [
        self::OPENED_ST       => 'открыта',
        self::CLOSED_ST       => 'закрыта',
        self::ENDED_ST        => 'завершена',
        self::READY_TO_PAY_ST => 'готова к оплате',
        self::NOT_PAYED_ST    => 'не оплачена',
        self::PAYED_ST        => 'оплачена',
        self::UNCONFIRMED_ST  => 'не подтверждена'
    ];
    private static $upperStateLabels = [
        self::OPENED_ST       => 'ОТКРЫТА',
        self::CLOSED_ST       => 'ЗАКРЫТА',
        self::ENDED_ST        => 'ЗАВЕРШЕНА',
        self::READY_TO_PAY_ST => 'ГОТОВА К ОПЛАТЕ',
        self::NOT_PAYED_ST    => 'НЕ ОПЛАЧЕНА',
        self::PAYED_ST        => 'ОПЛАЧЕНА',
        self::UNCONFIRMED_ST  => 'НЕ ПОДТВЕРЖДЕНА'
    ];
    private static $stateColors = [
        self::OPENED_ST       => 'red',
        self::CLOSED_ST       => 'blue',
        self::ENDED_ST        => 'olive',
        self::READY_TO_PAY_ST => 'green',
        self::NOT_PAYED_ST    => 'darkorchid',
        self::PAYED_ST        => '',
        self::UNCONFIRMED_ST  => ''
    ];
    private static $edgLabels = [
        0 => '',
        1 => 'ЕДГ',
    ];
    private static $pay_methodLabels = [
        self::PM_CARD       => 'Карта',
        self::PM_CASH       => 'Нал',
        self::PM_ACCOUNT    => 'Расчетный счет'
    ];

    private
        $pivotData = null,
        $workerIds = [],
        $parentWorkers = [
            '+' => [],
            'i' => []
        ],
        $workerHours = 0,
        $workerMoney = 0,
        $total_work_hours = 0,
        $totalMoney = 0;



    public function getMaskedClientPhone()
    {

    }

    /********************************************************************/

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function workers()
    {
        return $this->belongsToMany(Worker::class);
    }

    /********************************************************************/
    public function saveNds ()
    {
        if (!is_null($this->profit)) {
            $this->income_with_nds = $this->income + $this->income / 5;
            $this->nds = $this->income_with_nds - $this->income;
            $this->save();
        }
    }

    public function getPayedByClientLabel()
    {
        if ($this->payed_by_client) {
            return '(оплачена)';
        }
        else {
            return '(не оплачена)';
        }
    }

    public function addToReport ()
    {
        $alreadyAdded = Income::where('application_id', $this->id)->first();
        if (!$alreadyAdded) {
            $i = new Income();
            $i->text = $this->address . ', ' . $this->worker_count . ' чел';
            $i->date = $this->date;
            $i->money_amount = $this->profit;
            $i->activity_id = Activity::ID_MOVERS;
            $i->application_id = $this->id;
            $i->user_id = $this->dispatcher_id;
            $i->created_at = Carbon::now();
            $i->updated_at = Carbon::now();
            $i->save();
        }
        else {
            if ($alreadyAdded->money_amount != $this->profit) {
                $alreadyAdded->money_amount = $this->profit;
                $alreadyAdded->save();
            }
        }
    }

    public function setWorkerHours ($value)
    {
        $this->workerHours = $value;
    }

    public function saveTotalWorkHoursAndMoney($workHours)
    {
        $this->setWorkerHours($workHours);
        if ($this->hourly_job) {
            $this->total_work_hours = $this->workerHours * $this->worker_total;
            $this->income =  $this->total_work_hours * $this->price;
            $this->outcome = $this->total_work_hours * $this->price_for_worker;
        }
        else {
            $this->income   = $this->worker_total * $this->price;
            $this->outcome  = $this->worker_total * $this->price_for_worker;
            $this->total_work_hours = self::WORKDAY_HOURS * $this->worker_total;
        }
        $this->profit = $this->income - $this->outcome;
        $this->state = self::ENDED_ST;
        $this->save();
        $this->saveNds();
    }

    public function getPivotData()
    {
        if (is_null($this->pivotData)) {
            $this->pivotData =  DB::table('application_worker')
                ->where('application_id', $this->id)
                ->get();
        }
        return $this->pivotData;
    }

    public static function isThisOrdinaryWorker ($pivotTableRow)
    {
        return (
            is_null($pivotTableRow->parent_worker_id)
            &&
            is_null($pivotTableRow->relation_type)
        );
    }

    public static function isThisParentWorker ($pivotTableRow)
    {

        if (gettype($pivotTableRow) == 'array') {
            return (
                is_null($pivotTableRow['parent_worker_id'])
                    &&
                (
                    $pivotTableRow['relation_type'] == self::RT_PLUS ||
                    $pivotTableRow['relation_type'] == self::RT_INSTEAD
                )
            );
        }
        else if (gettype($pivotTableRow) == 'object') {
            return (
                is_null($pivotTableRow->parent_worker_id)
                    &&
                (
                    $pivotTableRow->relation_type == self::RT_PLUS ||
                    $pivotTableRow->relation_type == self::RT_INSTEAD
                )
            );
        }
    }

    public static function isThisChildWorker ($pivotRowAsObjOrArray)
    {
        if (gettype($pivotRowAsObjOrArray) == 'object') {
            return (
                !is_null($pivotRowAsObjOrArray->parent_worker_id)
            );
        }
        else if (gettype($pivotRowAsObjOrArray) == 'array') {
            return (
                !is_null($pivotRowAsObjOrArray['parent_worker_id'])
            );
        }
    }

    public function addWorker($pivotRow)
    {
        array_push($this->workerIds, $pivotRow->worker_id);
    }

    public function setParentPlusWorkerData($pivotRow)
    {
        $basicHours = $this->workerHours;
        $basicMoney = ($this->hourly_job)? $this->workerHours * $this->price_for_worker : 
            $this->price_for_worker;
        $this->setParentWorkerData($pivotRow, '+', $basicHours, $basicMoney);
    }

    public function setParentInsteadWorkerData($pivotRow)
    {
        $this->setParentWorkerData($pivotRow, 'i', 0 , 0);
    }

    public function setParentWorkerData($pivotRow, $relationType, $basicHours, $basicMoney)
    {
        if (!array_key_exists($pivotRow->parent_worker_id,
            $this->parentWorkers[$relationType]))
        {
            $this->parentWorkers[$relationType][$pivotRow->parent_worker_id]['total_hours'] = $basicHours;
            $this->parentWorkers[$relationType][$pivotRow->parent_worker_id]['total_money'] = $basicMoney;
        }

        $money = ($this->hourly_job)? $this->workerHours * $this->price_for_worker : $this->price_for_worker;

        $this->parentWorkers[$relationType][$pivotRow->parent_worker_id]['total_hours'] += $this->workerHours;
        $this->parentWorkers[$relationType][$pivotRow->parent_worker_id]['total_money'] += $money;
    }

    public function getParentWorkers()
    {
        return $this->parentWorkers;
    }

    public function deleteChildWorkers($parentWorkerId)
    {
        DB::table('application_worker')
            ->where('application_id', $this->id)
            ->where('parent_worker_id', $parentWorkerId)
            ->delete();
    }

    public function removeParentRelation($workerId)
    {
        $this->updatePivot($workerId, ['relation_type' => '']);
    }

    public function updatePivotWorkerHoursAndMoney($workerId, $workHours, $money)
    {
        $this->updatePivot($workerId, [
            'work_hours' => $workHours,
            'worker_money' => $money,
        ]);
    }

    public function updatePivot($workerId, $valArr)
    {
        DB::table('application_worker')
            ->where('application_id', $this->id)
            ->where('worker_id', $workerId)
            ->update($valArr);
    }

    public function updatePivotsByParent($parentWorkerId, $valArr)
    {
        DB::table('application_worker')
            ->where('application_id', $this->id)
            ->where('parent_worker_id', $parentWorkerId)
            ->update($valArr);
    }

    public function updatePivotAllWorkersHoursAndMoney ()
    {
        $money = ($this->hourly_job) ?
            $this->workerHours * $this->price_for_worker
            :
            $this->price_for_worker;

        $this->massUpdatePivot($this->workerIds, [
                'work_hours' => $this->workerHours,
                'worker_money' => $money
            ]);

        foreach (['+', 'i'] as $relType) {
            if (!empty($this->parentWorkers[$relType])) {
                foreach ($this->parentWorkers[$relType] as $id => $worker) {
                    $this->updatePivot($id, [
                        'total_parent_work_hours' => $worker['total_hours'],
                        'total_parent_money' => $worker['total_money'],
                    ]);
                }
            }
        }
    }

    public function massUpdatePivot(array $workersId, $valArr)
    {
        DB::table('application_worker')
            ->where('application_id', $this->id)
            ->whereIn('worker_id', $workersId)
            ->update($valArr);
    }


    public function updateParentPlusWorkers() {
        return $this->updateParentWorkers('+');
    }

    public function updateParentInsteadWorkers() {
        return $this->updateParentWorkers('i');
    }

    public function updateParentWorkers ($relationType)
    {
        foreach ($this->parentWorkers[$relationType] as $parentWorkerId => $data)
        {
            $this->updatePivot($parentWorkerId, [
                'total_parent_work_hours' => $data['total_hours'],
                'total_parent_money'      => $data['total_money']
            ]);
        }
    }


    public static function getStateLabels($textCase = 'capital') {
        if ($textCase == 'lower') {
            return self::$lowerStateLabels;
        }
        if ($textCase == 'upper') {
            return self::$upperStateLabels;
        }
        return self::$stateLabels;
    }

    public static function getShortcutStateLabels($textCase = 'capital') {
        $stateLabels = self::getStateLabels($textCase);
        $stateLabels[self::READY_TO_PAY_ST] = 'Готова';
        $stateLabels[self::UNCONFIRMED_ST] = 'Не подтв';
        if ($textCase == 'lower') {
            $stateLabels[self::READY_TO_PAY_ST] = 'готова';
            $stateLabels[self::UNCONFIRMED_ST] = 'не подтв.';
        }
        if ($textCase == 'upper') {
            $stateLabels[self::READY_TO_PAY_ST] = 'ГОТОВА';
            $stateLabels[self::UNCONFIRMED_ST] = 'НЕ ПОДТВ.';
        }
        return $stateLabels;
    }

    public static function getStateColors() {
        return self::$stateColors;
    }

    public function getLable($field, $value = null)
    {
        if ($field == 'price') {
            return $this->price . '/' . $this->price_for_worker;
        }

        if ($field == 'dispatcher') {
            $user = \App\User::find($this->dispatcher_id);
            if (!$user)
            {
                if ($this->dispatcher_id == User::ID_GLEB) {
                    return User::NAME_GLEB;
                }
                if ($this->dispatcher_id == User::ID_DIMA) {
                    return User::NAME_DIMA;
                }
                if ($this->dispatcher_id == User::ID_DEVOCHKA) {
                    return User::NAME_DEVOCHKA;
                }
                return User::NAME_SOMEONE;
            }
            else
            {
                return $user->name;
            }
            /**/
        }

        $var = $field . 'Labels';
        return self::$$var[$value];
    }

    public function saveWorkerCount()
    {
        $workers =  DB::table('application_worker')
            ->where('application_id', $this->id)->get();

        $insteadWorkersParents = [];
        foreach ($workers as $w) {
            if ($w->relation_type == self::RT_INSTEAD && $w->parent_worker_id == null) {
                array_push($insteadWorkersParents, $w->worker_id);
            }
        }
        $this->worker_count = count($workers) - count($insteadWorkersParents);
        $this->save();
    }

    public function getWorkerCount() {
        $workers =  DB::table('application_worker')
            ->where('application_id', $this->id)->get();

        $insteadWorkersParents = [];
        foreach ($workers as $w) {
            if ($w->relation_type == self::RT_INSTEAD && $w->parent_worker_id == null) {
                array_push($insteadWorkersParents, $w->worker_id);
            }
        }
        return count($workers) - count($insteadWorkersParents);
    }

    public function changeState() {
        if ($this->state == self::OPENED_ST || $this->state == self::CLOSED_ST) {
            if ($this->worker_count < $this->worker_total) {
                $this->state = self::OPENED_ST;
            }
            else if ($this->worker_count == $this->worker_total) {
                $this->state = self::CLOSED_ST;
            }
        }
        $this->save();
    }
}

/*
 * Заявка ❗
 * Садовая 25б
 * 13:00
 * 2чел
 * 200рч
 * Выгрузка зоотоваров
 */