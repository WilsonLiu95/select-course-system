<?php

use Illuminate\Database\Seeder;

class DirectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dir = [
            '多媒体信息处理方向','大数据处理方向','网络应用方向','智能电路系统方向','数字信号处理方向','机器学习与搜索方向','健康医疗信息处理方向'
        ];
        foreach ($dir as $index => $item){
            factory(\App\Model\Direction::class)->create([
                'name'=>$item,
                'direction_code'=> $index+1 // 方向代码
            ]);
        }
    }
}
