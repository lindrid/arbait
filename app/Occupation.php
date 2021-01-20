<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    protected $table = "occupations";

    public $primaryKey = "id";
    public $incrementing = "id";

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public static function mainCategory()
    {
        return "Основные работы";
    }
}
