<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebitCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debit_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number', 150); // номер карты / номер телефона / сопутствующая информация
            $table->string('phone_number', 18);
            $table->string('bank', 2); //sb(erbank), ti{nkoff), ro(cket bank), mt(s)
            $table->unsignedInteger('sent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debit_cards');
    }
}
