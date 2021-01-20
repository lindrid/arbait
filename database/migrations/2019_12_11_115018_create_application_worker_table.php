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
            $table->integer('debit_card_id')->unsigned();
            $table->float('work_hours')->unsigned()->nullable()->default(null);
            $table->float('worker_money')->nullable()->default(0);
            $table->float('total_parent_work_hours')->unsigned()->nullable()->default(0);
            $table->float('total_parent_money')->unsigned()->nullable()->default(0);
            $table->boolean('worker_got_money')->default(0);
            $table->integer('parent_worker_id')->unsigned()->nullable()->default(null);
            // relation_type: '+' - это значит рабочий вышел ВМЕСТЕ с parent рабочим
            // relation_type: 'i' - это значит рабочий вышел ОТ parent рабочего (диспетчера, рабочего дома)
            $table->string('relation_type', 1)->unsigned()->nullable()->default(null);
            $table->timestamps();

            $table->primary(['application_id', 'worker_id']);
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
