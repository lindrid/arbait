<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Application;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(with(new Application)->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'address', 120);
            $table->string( 'date', 10);
            $table->string( 'time', 5);
            $table->unsignedSmallInteger('price');
            $table->unsignedSmallInteger('price_for_worker');
            $table->boolean('hourly_job');
            $table->string( 'what_to_do');
            $table->unsignedSmallInteger('total_work_hours');
            $table->boolean('edg')->default(0);
            $table->tinyInteger('pay_method'); //описаны в модели Application
            $table->string('client_phone_number', 30);
            $table->tinyInteger('state'); //описаны в модели Application
            $table->boolean('payed_by_client')->default(0);
            $table->integer(    'income')->nullable()->default(null);
            $table->integer(    'outcome')->nullable()->default(null);
            $table->integer(    'profit')->nullable()->default(null);
            $table->integer(    'income_with_nds')->nullable()->default(null);
            $table->integer(    'nds')->nullable()->default(null);
            $table->tinyInteger('worker_count')->unsigned(); // сколько собрано
            $table->tinyInteger('worker_total')->unsigned(); // всего рабочих
            $table->unsignedInteger('dispatcher_id')->default(0);
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
        Schema::dropIfExists(with(new Application)->getTable());
    }
}
