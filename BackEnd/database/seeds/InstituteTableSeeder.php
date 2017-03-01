<?php

use Illuminate\Database\Seeder;

class InstituteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Model\Institute::class)->create();
    }
}
