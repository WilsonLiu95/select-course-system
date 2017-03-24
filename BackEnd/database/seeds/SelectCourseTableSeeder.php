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
        $grade_list = [1,2,3];
        $one_grade_student_num = 250;
        foreach ($grade_list as $item) {
            factory(\App\Model\SelectCourse::class,100)->create([
                'grade_id'=>$item,
                'student_id'=> random_int(($item-1)*$one_grade_student_num+1, $item*$one_grade_student_num)
            ]);
        }

    }
}
