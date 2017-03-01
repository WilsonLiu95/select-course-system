<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institute_id');
            $table->string('name',32);
            // 个性化配置
            $table->tinyInteger('system_status')
                ->comment("状态包含 0:关闭,1:对特权班级开发,2:对所有学生开放。关闭中的年份只要不结束都可以再次开放");
            $table->boolean('isHistory')
                ->default(false)
                ->comment("已经结束选课的年份,结束后的年份将不能再开放");
            $table->integer('min_credit')
                ->comment("最少学分限制");
            $table->integer('max_prior_select_num')
                ->comment("对于特权班级,每门课程最多有多少人选上");
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
        Schema::drop('grade');
    }
}
