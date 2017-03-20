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
            $table->integer('institute_id');
            $table->boolean('is_common');
            $table->boolean('is_select');
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
