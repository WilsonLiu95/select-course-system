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
        $class = [
            '通信1301','通信1302','通信1303','通信1304','通信1305','通信1306',
            '电信1301','电信1302','电信1303','电信1304','电信1305','电信1306',
        ];
        $prior = [
            '卓越班','提高班'
        ];
        foreach($class as $v){
            factory(\App\Model\Classes::class)->create([
                'name'=> $v,
                'is_prior'=>false
            ]);
        }
        foreach($prior as $v){
            factory(\App\Model\Classes::class)->create([
                'name'=> $v,
                'is_prior'=>true
            ]);
        }
    }
}
