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
            $table->integer('grade_id');

            $table->integer('direction_id')
                  ->comment('属于哪个专业方向');

            $table->boolean('is_common')
                ->comment('是否为公选课');
            $table->boolean('is_select')
                ->comment('是否为选修课');

            $table->string('title',32);
            $table->integer('credit')
                ->comment('学分');
            $table->integer('required_number')
                ->comment('所需学生数量');

            $table->tinyInteger('status')
                ->comment("状态包含 0:已删除,1:有效")
                ->default(1);


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
