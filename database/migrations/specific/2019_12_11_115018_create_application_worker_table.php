<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationWorkerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_worker', function (Blueprint $table) {
            $table->integer('application_id')->unsigned();
            $table->integer('worker_id')->unsigned();
            $table->integer('debet_card_id')->unsigned();
            $table->tinyInteger('work_hours');
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
        Schema::dropIfExists('application_worker');
    }
}
