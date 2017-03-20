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
            $table->integer('grade_id');
            // 用于映射两者的多对多关系
            $table->string('name',32)
                ->comment('班级名称');

            $table->tinyInteger('classes_code')
                ->comment('班级代码,提供给教务科填写EXCEL,如果没有就增加学生选取班级的步骤');

            //  首先判断是否录入了班级,
            // 1-1.  如果录入了班级,且录入了专业直接放行
            // 1-2. 录入了班级但是没有录入专业,则去检索班级,此时班级如果隶属于某一专业则直接填充学生表的major_code
            // 1-3. 如果班级没有隶属于任何一个专业,则提供给学生专业选项

            $table->string('major_id');
            $table->integer('major_code')
                ->comment('专业代号,如果没有则代表该班级的人可以任意选择专业');
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
        Schema::drop('classes');
    }
}
