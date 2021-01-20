<?php

namespace App\Instagram;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    public function iPublic()
    {
        return $this->belongsTo(IPublic::class,  'insta_order_id');
    }

    public function income()
    {
        return $this->hasOne(Income::class);
    }

    public function outcome()
    {
        return $this->hasOne(Outcome::class);
    }
}
