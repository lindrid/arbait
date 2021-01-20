<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstaOutcome extends Model
{
    protected $fillable = ['date', 'money_amount', 'application_id', 'user_id'];
    protected $table = "insta_outcome";
}
