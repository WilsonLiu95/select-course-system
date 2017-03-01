<?php

use Illuminate\Database\Seeder;

class MajorDirectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 专业与方向的映射表
        $map = [
            1=>[1,2,3,7], // 电信映射表
            2=>[2,3,4,5], // 通信映射表
            3=>[1,2,6,7] //微波映射表
        ];

        foreach($map as $key=>$value){
            foreach($value as $v){
                factory(\App\Model\MajorDirection::class)->create([
                    'direction_id'=> $v,
                    'major_id'=>$key,

                ]);
            }
        }
    }
}
