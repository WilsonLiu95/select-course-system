<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('direction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institute_id');
            $table->tinyInteger('status')
                ->default(1)
                ->comment("为防止以后新增或删除方向预留字段");
            $table->string('name', 32);
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
        Schema::drop('direction');
    }
}
