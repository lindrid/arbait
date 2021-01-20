<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payments";

    public $primaryKey = "id";
    public $incrementing = "id";

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function defaultCardId() {
        return $this->mySberCardId();
    }

    public function dimasSberCardId() {
        return 1;
    }

    public function mySberCardId() {
        return 2;
    }

    public function mtsCardId() {
        return 3;
    }

    public function tinkoffCardId() {
        return 4;
    }
}
