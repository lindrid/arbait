<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPhone extends Model
{
    //
    protected $table = "user_phones";

    public $primaryKey = "user_id";

    protected $fillable = [
        'number',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
