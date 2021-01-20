<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\UserPhone;

class CreateUserPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(with(new UserPhone)->getTable(), function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string( 'number', 25);
            $table->string( 'type', 2); // 'c', 'w', 'cw' = call, whatsapp, call and whatsapp
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
        Schema::dropIfExists('user_phones');
    }
}
