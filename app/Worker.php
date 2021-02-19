<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Worker extends Model
{
    protected $table = "workers";
    protected $fillable = ['name', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function applications()
    {
        return $this->belongsToMany(Application::class);
    }

    public function phones()
    {
        return $this->hasMany(WorkerPhone::class, 'worker_id');
    }

    public function debitCards()
    {
        return $this->belongsToMany('App\DebitCard');
    }

    public static function anyCardRelationExists ($worker)
    {
        if (gettype($worker) == 'object') {
            $workerId = $worker->id;
        }
        if (gettype($worker) == 'array') {
            $workerId = $worker['id'];
        }
        return DB::table('debit_card_worker')
            ->where('worker_id',  $workerId)
            ->exists();
    }

    public function getLatestDebitCardRelation()
    {
        return DB::table('debit_card_worker')
            ->where('worker_id', $this->id)
            ->whereNotIn('debit_card_id', DebitCard::IDS_NO_CARD)
            ->latest()
            ->first();
    }

    public static function ifRelationWithParentIsPlus ($appWorker) {
        return ($appWorker->relation_type == '+');
    }

    public static function ifRelationWithParentIsInstead ($appWorker) {
        return ($appWorker->relation_type == 'i');
    }

    public function updateVal($valArr)
    {
        return DB::table('workers')
            ->where('id', $this->id)
            ->update($valArr);
    }
}
