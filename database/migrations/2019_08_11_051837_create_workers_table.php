<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Worker;

class CreateWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(with(new Worker)->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'name',100);
            $table->integer( 'age')->nullable();
            $table->unsignedInteger( 'district_id')->nullable();
            $table->string( 'address', 120)->nullable();
            $table->string( 'pass_series_number')->nullable();
            $table->string(   'birth_date', 10)->nullable();
            $table->string(   'pass_date', 10)->nullable();
            $table->string( 'pass_code', 7)->nullable();
            $table->string( 'inn')->nullable();
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
        Schema::dropIfExists(with(new Worker)->getTable());
    }
}
