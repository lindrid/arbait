<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Outcome;

class CreateOutcomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(with(new Outcome)->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('date', 10);
            $table->unsignedInteger('money_amount');
            $table->unsignedInteger('activity_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('insta_public_id')->default(0);
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
        Schema::dropIfExists(with(new Outcome)->getTable());
    }
}
