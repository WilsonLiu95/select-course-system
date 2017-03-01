<?php

use Illuminate\Database\Seeder;

class StudentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $major_code = rand(1,3); // 先确认专业代码
        $major_id = App\Model\Major::where('institute_id',1)
            ->where('major_code',$major_code)->value('id');

        $classes_code = '';

        factory(\App\Model\Student::class)->make([


        ]);
    }
}
