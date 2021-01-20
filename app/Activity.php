<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Income;
use App\IncomeInsta;

class Activity extends Model
{
    protected $table = 'activities';
    public const ID_MOVERS = 1;
    public const ID_INSTAGRAM = 2;

    public const NAME_MOVERS = 'Грузчики';
    public const NAME_INSTAGRAM = 'Instagram';

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function instaIncomes()
    {
        return $this->hasMany(IncomeInsta::class);
    }
}
