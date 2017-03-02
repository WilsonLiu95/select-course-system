<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student', function (Blueprint $table) {
            $table->increments('id');
            $table->char('openid',28); // 微信的uid来标识唯一的微信号
            $table->integer('grade_id');

            $table->integer('institute_id');
            // 确定学院之后,直接通过专业代号和课程代号去查询相应的课程在数据库中的ID再进行填充

            $table->tinyInteger('major_code')
                ->comment('专业代码,如果专业代码为0,则表示自由选择专业的班级');
            $table->integer('major_id'); // 专业ID

            $table->integer('classes_code')
                ->comment('班级代码,根据班级代码以及学院ID年份ID确认班级ID'); // 班级ID

            // 这三个自已选择
            $table->integer('classes_id'); // 班级ID
            $table->integer('direction_id'); // 方向ID



            $table->string('name',32);
            $table->string('job_num',32)->comment("学号");
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
        Schema::drop('student');
    }
}
