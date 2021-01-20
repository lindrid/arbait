<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    protected $table = 'privileges';

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
