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
        $grade_list = [
            1, 2, 3
        ];
        foreach ($grade_list as $item) {
            factory(\App\Model\Student::class,247)->create([
                'grade_id'=>$item
            ]);
        }

    }
}
