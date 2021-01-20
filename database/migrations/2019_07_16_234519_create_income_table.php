<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Income;

class CreateIncomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(with(new Income)->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('text');
            $table->boolean('was_parsed');
            $table->unsignedInteger('money_amount');
            $table->unsignedInteger('application_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
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
        Schema::dropIfExists(with(new Income)->getTable());
    }
}
