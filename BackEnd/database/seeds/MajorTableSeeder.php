<?php

use Illuminate\Database\Seeder;

class MajorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $major = [
            '电子信息','通信工程','微波技术'
        ];
        foreach ($major as $key=>$item){
            factory(\App\Model\Major::class)->create([
                'name'=>$item,
                'major_code'=> $key+1 // 课程代号
            ]);
        }
    }
}
