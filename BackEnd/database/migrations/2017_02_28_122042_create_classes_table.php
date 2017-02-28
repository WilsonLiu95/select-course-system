<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institute_id');
            // 用于映射两者的多对多关系
            $table->string('name',32)
                ->comment('班级名称');
            $table->integer('grade_id');
            $table->boolean('is_prior')
                ->comment('是否优先,即提高卓越');
            $table->tinyInteger('status')
                ->default(1)
                ->comment("为防止以后新增或删除方向预留字段");

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
        Schema::drop('classes');
    }
}
