<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMajorDirectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('major_direction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institute_id');

            // 用于映射两者的多对多关系
            $table->integer('major_id');
            $table->integer('direction_id');

            $table->softDeletes();
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
        Schema::drop('major_direction');
    }
}