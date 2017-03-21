<?php

use Illuminate\Database\Seeder;

class GradeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $grade_list = [
            '2015','2016','2017'
        ];
        foreach($grade_list as $index=>$item){
            factory(\App\Model\Grade::class)->create(
                [

                    'name'=>$item
                ]
            );
        }

    }
}
