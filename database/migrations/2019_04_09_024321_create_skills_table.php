<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->unsignedInteger('occupation_id');
            $table->unsignedInteger('parent_id');
            /*
             *  same skill id
                если same_skill_id <> 0 => значение этого поля ссылается
                на id такого же навыка, который находится где-то в
                другой специальности
            */
            $table->unsignedInteger('same_skill_id')->default(0);

            $table->foreign('occupation_id')
                ->references('id')
                ->on('occupations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skills');
    }
}
