<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $table = "skills";

    public $primaryKey = "id";
    public $incrementing = "id";

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
