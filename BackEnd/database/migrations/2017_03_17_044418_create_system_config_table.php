<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_config', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institute_id');

            $table->boolean('is_common_open')
                ->comment('公选课 是否开放选择');
            $table->integer('min_common_credit')
                ->comment("公选课,最少学分限制");
            $table->integer('max_common_credit')
                ->comment("公选课,上限学分限制");

            $table->boolean('is_direction_open')
                ->comment('选修课 是否开放选择');
            $table->integer('min_direction_credit')
                ->comment("专业方向课,最少学分限制");
            $table->integer('max_direction_credit')
                ->comment("专业方向课,上限学分限制");

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
        Schema::drop('system_config');
    }
}
