<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();



        // 暂时一条数据部分
        $this->call(InstituteTableSeeder::class);
        $this->call(AdminTableSeeder::class);
        $this->call(GradeTableSeeder::class);

        // 单独设定
        $this->call(MajorDirectionTableSeeder::class);
        $this->call(DirectionTableSeeder::class);
        $this->call(MajorTableSeeder::class);
        $this->call(ClassesTableSeeder::class);

        $this->call(CourseTableSeeder::class); // 课程
        $this->call(StudentTableSeeder::class); // 学生表


        $this->call(ScheduleTableSeeder::class);

        Model::reguard();
    }
}
