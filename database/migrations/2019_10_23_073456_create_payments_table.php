<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Payment;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(with(new Payment)->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('date', 10);
            $table->tinyInteger('state'); //открыт(1), нажат(2), закрыт(3)
            $table->integer('money_amount');
            $table->unsignedInteger('debit_card_id');
            $table->unsignedInteger('application_id');
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
        Schema::dropIfExists(with(new Payment)->getTable());
    }
}
