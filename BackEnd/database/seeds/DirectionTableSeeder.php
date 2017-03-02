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
            '多媒体信息处理','大数据处理','网络应用','智能电路系统','数字信号处理','机器学习与搜索','健康医疗信息处理'
        ];
        foreach ($dir as $index => $item){
            factory(\App\Model\Direction::class)->create([
                'name'=>$item,
                'direction_code'=> $index+1 // 方向代码

            ]);
        }
    }
}
