<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('select_course', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institute_id');
            $table->integer('grade_id');
            $table->integer('direction_id')
                ->comment('代表此条选课记录属于哪个方向,0:公选课,其他则为具体方向的id值');

            $table->integer('course_id');
            $table->integer('student_id');


            $table->boolean('isQuit')
                ->comment('是否退选')
                ->default(false);
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
        Schema::drop('select_course');
    }
}
