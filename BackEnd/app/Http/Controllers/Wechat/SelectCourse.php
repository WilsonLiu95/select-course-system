<?php

namespace App\Http\Controllers\Wechat;

use App\Model\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SelectCourse extends Controller
{
    use BaseTrait;
    public function getIndex(){
        $user = $this->getUser();
        $data['major_id'] = $user->major_id;
        $data['classes_id'] = $user->classes_id;
        $data['direction_id'] = $user->direction_id;
        return $this->json(1,$user);
    }
    public function getCanSelectCourse(){
        $dir_id = $this->getUser()->direction_id;
        $course = Course::where('direction_id',$dir_id)->get();
        return $this->json(1,$course);
    }
    public function postSelectCourse(){

    }
}
