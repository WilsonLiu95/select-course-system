<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        factory(\App\Model\Institute::class)->create();
        factory(\App\Model\Admin::class)->create();

        factory(\App\Model\Grade::class)->create();

        factory(\App\Model\Major::class,3)->create();
        factory(\App\Model\Direction::class,7)->create();
        factory(\App\Model\MajorDirection::class,10)->create();

        factory(\App\Model\Classes::class,10)->create();
        factory(\App\Model\Course::class,77)->create()->each(function($course){
            $schedule = factory(\App\Model\Schedule::class)->make();
            $course->schedule()->save($schedule);
        });

        factory(\App\Model\Student::class,237)->create();
        Model::reguard();
    }
}
