<?php

namespace App\Instagram;

use Illuminate\Database\Eloquent\Model;
use App\Activity;

class Outcome extends \App\Outcome
{
    protected $table = "outcome";

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function($query) {
            return $query->where('activity_id', Activity::ID_INSTAGRAM);
        });
    }

    public function iPublic()
    {
        return $this->belongsTo(IPublic::class, 'public_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'insta_order_id');
    }
}
