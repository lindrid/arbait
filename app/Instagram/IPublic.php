<?php

namespace App\Instagram;

use Illuminate\Database\Eloquent\Model;

class IPublic extends Model
{
    protected $table = "insta_publics";

    public const ID_RABOTA_VDK = 1;
    public const ID_MODELS_VDK = 2;

    public const NAME_RABOTA_VDK = '@rabota.vdk';
    public const NAME_MODELS_VDK = '@modelsvdk';

    public const VAL_RABOTA_VDK = 100;
    public const VAL_MODELS_VDK = 100;

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function outcomes()
    {
        return $this->hasMany(Outcome::class);
    }
}
