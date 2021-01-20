<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    public const DIMAS_PROCENT = 0.1; // 10% of profit

    //protected $fillable = ['text', 'was_parsed', 'money_amount', 'application_id', 'user_id'];
    protected $table = "income";

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
