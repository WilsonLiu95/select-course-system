<?php

use Illuminate\Database\Seeder;

class ClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $class_one = [
            '通信01','通信02','通信03','通信04','通信05','通信06',
        ];
        $class_two = [
            '电信01','电信02','电信03','电信04','电信05','电信06',
        ];
        $class_three = [
            '微波01','微波02','微波03','微波04','微波05','微波06',
        ];
        $prior = [
            '卓越班','提高班'
        ];
        $grade_list = [
            1, 2, 3
        ];
        foreach($grade_list as $grade){
        foreach($class_one as $key=>$v){
            factory(\App\Model\Classes::class)->create([
                'name'=> $v,
                'major_code'=>1,
                'classes_code'=> $key+1,
                'grade_id'=>$grade,
                'major_id'=>1
            ]);
        }
        foreach($class_two as $key=>$v){
            factory(\App\Model\Classes::class)->create([
                'name'=> $v,
                'major_code'=>2,
                'classes_code'=> $key+7,
                'grade_id'=>$grade,
                'major_id'=>2
            ]);
        }
        foreach($class_three as $key=>$v){
            factory(\App\Model\Classes::class)->create([
                'name'=> $v,
                'classes_code'=> $key+13,
                'major_code'=>3,
                'grade_id'=>$grade,
                'major_id'=>3
            ]);
        }
        foreach($prior as $key=>$v){
            factory(\App\Model\Classes::class)->create([
                'name'=> $v,
                'major_code'=>0,
                'grade_id'=>$grade,
                'classes_code'=> $key+19,
                'major_id'=>0,
            ]);
        }
        }

    }
}
