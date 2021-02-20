<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkerPhone extends Model
{
    //
    protected $table = "worker_phones";
    protected $fillable = ['number', 'type', 'worker_id'];
    protected $primaryKey = 'number';

    public const PHONE_TYPE_CALL = 'c';
    public const PHONE_TYPE_WHATSAPP = 'w';
    public const PHONE_TYPE_CALL_WHATSAPP = 'cw';

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}
