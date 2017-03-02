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
            '通信1301','通信1302','通信1303','通信1304','通信1305','通信1306',
        ];
        $class_two = [
            '电信1301','电信1302','电信1303','电信1304','电信1305','电信1306',
        ];
        $class_three = [
            '微波1301','微波1302','微波1303','微波1304','微波1305','微波1306',
        ];
        $prior = [
            '卓越班','提高班'
        ];
        foreach($class_one as $v){
            factory(\App\Model\Classes::class)->create([
                'name'=> $v,
                'is_prior'=>false,
                'major_code'=>1,
                'major_id'=>1
            ]);
        }
        foreach($class_two as $v){
            factory(\App\Model\Classes::class)->create([
                'name'=> $v,
                'is_prior'=>false,
                'major_code'=>2,
                'major_id'=>2
            ]);
        }
        foreach($class_three as $v){
            factory(\App\Model\Classes::class)->create([
                'name'=> $v,
                'is_prior'=>false,
                'major_code'=>3,
                'major_id'=>3
            ]);
        }
        foreach($prior as $v){
            factory(\App\Model\Classes::class)->create([
                'name'=> $v,
                'major_code'=>0,
                'major_id'=>0,
                'is_prior'=>true
            ]);
        }
    }
}
