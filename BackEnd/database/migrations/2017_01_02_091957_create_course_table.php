<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')
                ->comment('用于内存中特殊标识某门课程');
            $table->integer('institute_id');
            $table->integer('grade_id');

            $table->integer('direction_id')
                  ->comment('属于哪个专业方向');
            $table->integer('direction_code')
                ->comment('用于excel');
            $table->tinyInteger('course_type')
                ->comment('1:单纯的公选课，2:单纯的选修课，3:即是公选课又是选修课');
            $table->string('course_code',10)
                ->comment('国家给的课程编号');
            $table->string('teacher',32);
            $table->string('title',32);
            $table->string('detail',256);
            $table->integer('credit')
                ->comment('学分');
            $table->integer('required_number')
                ->comment('所需学生数量');


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
        Schema::drop('course');
    }
}
