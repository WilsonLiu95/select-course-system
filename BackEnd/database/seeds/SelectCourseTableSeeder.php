<?php

use Illuminate\Database\Seeder;

class SelectCourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Model\SelectCourse::class,100)->create();
    }
}
