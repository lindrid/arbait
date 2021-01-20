<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const GUEST_ID = 4;

    protected $table = 'roles';

    public function privileges()
    {
        return $this->belongsToMany(Privilege::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
