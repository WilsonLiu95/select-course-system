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

            $table->boolean('is_common')
                ->comment('是否为公选课');
            $table->boolean('is_select')
                ->comment('是否为选修课');

            $table->string('title',32);
            $table->string('course_code',32)
                ->comment('国家给的课程编号');
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
