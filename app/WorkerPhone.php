<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkerPhone extends Model
{
    //
    protected $table = "worker_phones";
    protected $fillable = ['number', 'type', 'worker_id'];
    protected $primaryKey = 'number';

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}
