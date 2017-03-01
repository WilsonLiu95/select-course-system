<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMajorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('major', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institute_id');
            $table->tinyInteger('major_code')
                ->comment('专业代号用于填充在excel中,根据institute与专业代号一起确认方向ID。同时如果专业代号为0');
            $table->string('name', 32);
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
        Schema::drop('major');
    }
}
