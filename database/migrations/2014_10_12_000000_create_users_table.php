<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(with(new User)->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'fullname',100);
            $table->string( 'phone_call', 25);
            $table->string( 'phone_whatsapp', 30);
            $table->string( 'pass_series_number')->nullable();
            $table->string(   'birth_date', 10);
            $table->string(   'pass_date', 10)->nullable();
            $table->string( 'pass_code', 7)->nullable();
            $table->string( 'inn')->nullable();
            $table->string( 'address', 120);
            $table->string( 'password');
            $table->string( 'type', 3);
            $table->boolean('RF_citizen');
            $table->string( 'foreign_pass_series_number')->nullable();
            $table->string('passport_image')->nullable();
            $table->boolean('approved');
            $table->boolean('verified_phone');
            $table->rememberToken();
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
        Schema::dropIfExists(with(new User)->getTable());
    }
}
