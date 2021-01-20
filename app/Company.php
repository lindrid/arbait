<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'address', 'website', 'email', 'user_name'];

    public function user()
    {
        //asdsad
        return $this->belongsTo(User::class);
    }
}
