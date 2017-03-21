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
            $table->integer('major_id')  
                ->comment('专业ID');
            $table->tinyInteger('classes_code')
                ->comment('班级代码,根据班级代码以及学院ID年份ID确认班级ID，0代表由用户自己去选择班级'); 
            $table->integer('classes_id')
                ->comment('班级ID');  

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
