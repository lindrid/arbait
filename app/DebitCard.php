<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DebitCard extends Model
{
    //
    public const TABLE = "debit_cards";
    public const IDS_NO_CARD = [0, 1];

    protected $table = "debit_cards";
    protected $fillable = ['number'];

    public static function IS_NOT_EMPTY ($debitCardId)
    {
        return ($debitCardId != 0) && ($debitCardId != 1);
    }

    public function isNotEmpty ()
    {
        return ($this->id != 0) && ($this->id != 1);
    }

    public function cardIsEmpty ()
    {
        return ($this->id == 0) || ($this->id == 1);
    }

    public function workers()
    {
        return $this->belongsToMany('App\Worker');
    }

    public static function updateNumber ($id, $number)
    {
        return DB::table(self::TABLE)
            ->where('id', $id)
            ->update(['number' => $number]);
    }

    public function insertWorkerCardRelation($worker)
    {
        if (gettype($worker) == 'object') {
            $workerId = $worker->id;
        }
        if (gettype($worker) == 'array') {
            $workerId = $worker['id'];
        }
        if (!$this->anyWorkerCardRelationExists())
        {
            return DB::table('debit_card_worker')->insert([
                'debit_card_id' => $this->id,
                'worker_id' => $workerId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }

    public function anyWorkerCardRelationExists ()
    {
        return DB::table('debit_card_worker')
            ->where('debit_card_id',  $this->id)
            ->exists();
    }

    public function updateWorkerCardRelation($worker)
    {
        $dcId = $this->id;

       /* if ($this->cardIsEmpty()) {
            $newCard =  DB
                ::table('debit_cards')
                ->where('number', $worker['debit_card'])
                ->latest()
                ->first();
            if (!$newCard) {
                $dcId = $this->inse
            }
            else {
                $dcId = $newCard->id;
            }
        }*/

        if (gettype($worker) == 'object') {
            $workerId = $worker->id;
        }
        if (gettype($worker) == 'array') {
            $workerId = $worker['id'];
        }
        return DB::table('debit_card_worker')
            ->where('worker_id', $workerId)
            ->update([
                'debit_card_id' => $dcId,
                'updated_at' => Carbon::now()
            ]);
    }

    public function bankName() {
        switch ($this->bank) {
            case 'sb':
                return 'Сбербанк';
                break;
            case 'ti':
                return 'Тинькофф';
                break;
            case 'vt':
                return 'ВТБ';
                break;
        }
    }
}
