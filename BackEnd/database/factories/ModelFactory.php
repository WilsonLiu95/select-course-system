<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/


$factory->define(\App\Model\Admin::class, function ($faker) {
    return [
        'institute_id'=>1,
        "account" => 19951995,
        "password" => 19951995,
    ];
});

$factory->define(\App\Model\Major::class, function ($faker) {
    return [
        'name' => $faker->name,
        'institute_id' => 1,
        'major_code'=>1
    ];
});

$factory->define(\App\Model\DirectionMajor::class, function ($faker) {
    return [
        'direction_id'=>$faker->shuffle([1,2,3,4,5,6,7])[0],
        'major_id' => $faker->shuffle([1,2,3])[0],

    ];
});
$factory->define(\App\Model\CourseDirection::class, function ($faker) {
    $code = $faker->shuffle([1,2,3,4,5,6,7])[0];
    return [
        'direction_id'=>$code,
        'course_id' => $faker->numberBetween($min = 1, $max = 77),

    ];
});
$factory->define(\App\Model\Direction::class, function ($faker) {
    return [
        'institute_id' => 1,
    ];
});
$factory->define(\App\Model\Grade::class, function ($faker) {
    return [
        'id'=>1,
        'name' => 2013,
        'institute_id' => 1,
    ];
});
$factory->define(\App\Model\Classes::class, function ($faker) {
    return [
        'name' => $faker->name,
        'institute_id' => 1,
        "grade_id"=>1,
    ];
});
$factory->define(\App\Model\Institute::class, function ($faker) {
    return [
        'id' =>1,
        'name' => "电信学院",
    ];
});

$factory->define(\App\Model\Student::class, function ($faker) {
    return [
        "grade_id"=>1,
        "institute_id" =>1,
        "name" => $faker->name,
        'major_id'=>0,
        "job_num" => $faker->randomNumber($nbDigits = NULL),
    ];
});



$factory->define(\App\Model\SelectCourse::class, function ($faker) {
    return [
        'institute_id' => 1,
        'grade_id'=>1,
        'student_id' => $faker->numberBetween($min = 1, $max = 237),
        "course_id"=> $faker->numberBetween($min = 1, $max = 77),
    ];
});

$factory->define(\App\Model\Course::class, function ($faker) {

    return [
        'institute_id' => 1,
        'teacher'=> $faker->name,
        'is_common' => false,
        "is_select"=>false,
        'detail'=> $faker->text,
        'credit' => $faker->shuffle([2,3,4,5])[0],
        'required_number' => $faker->numberBetween($min = 30, $max = 100),
        'title'=>$faker->word,
    ];

});

$factory->define(\App\Model\SystemConfig::class, function($faker){
    return [
        'institute_id' => 1,
        'is_common_open' => true,
        'min_common_credit'=>6,
        'max_common_credit'=>10,
        'is_direction_open'=>true,
        'min_direction_credit'=>10,
        'max_direction_credit'=>15,
    ];
});