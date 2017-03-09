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

            $table->integer('course_id');
            $table->integer('student_id');

//            $table->tinyInteger('status')
//            ->comment("0:为选中后退选课程,1:选中课程");
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
