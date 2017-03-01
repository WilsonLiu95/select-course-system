<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('direction', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('institute_id');
            $table->integer('direction_code')
                ->comment('方向代号用于填充在excel中,根据institute与方向代号一起确认方向ID');
            $table->softDeletes();
            $table->integer('direction_id');
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
        Schema::drop('direction');
    }
}
