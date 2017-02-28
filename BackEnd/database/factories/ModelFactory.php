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
    ];

});

$factory->define(\App\Model\MajorDirection::class, function ($faker) {
    return [
        'institute_id' => 1,
        'direction_id'=>$faker->shuffle([0,1,2,3,4,5,6])[0],
        'major_id' => $faker->shuffle([1,2,3])[0],
    ];
});
$factory->define(\App\Model\Direction::class, function ($faker) {
    return [
        'name' => $faker->name,
        'institute_id' => 1,
    ];
});
$factory->define(\App\Model\Grade::class, function ($faker) {
    return [
        'id'=>1,
        'name' => 2013,
        'status'=> 1,
        'min_credit'=>15,
        'max_prior_select_num'=>5,
        'institute_id' => 1,

    ];
});
$factory->define(\App\Model\Classes::class, function ($faker) {
    return [
        'name' => $faker->name,
        'institute_id' => 1,
        "grade_id"=>1,
        'is_prior'=>$faker->shuffle([true,false])[0],
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
        "job_num" => $faker->randomNumber($nbDigits = NULL),
    ];
});



$factory->define(\App\Model\Schedule::class, function ($faker) {
    return [
        'status' => $faker->shuffle([0,1])[0],
        'grade_id'=>1,
        'student_id' => $faker->numberBetween($min = 1, $max = 237),
        "course_id"=> $faker->numberBetween($min = 1, $max = 77),
    ];
});

$factory->define(\App\Model\Course::class, function ($faker) {

    return [
        'institute_id' => 1,
        'grade_id' => 1,
        'is_common' => $faker->shuffle([true,false])[0],
        'is_select' =>$faker->shuffle([true,false])[0],
        'direction_id'=>$faker->shuffle([0,1,2,3,4,5,6])[0],
        'credit' => 15,
        'required_number' => $faker->numberBetween($min = 30, $max = 100),
        'title'=>$faker->word,
        'status' => $faker->shuffle([0,1])[0],


    ];
});
