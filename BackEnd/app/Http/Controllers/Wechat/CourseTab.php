<?php

namespace App\Http\Controllers\Wechat;

use App\Model;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;




class CourseTab extends Controller
{
    use BaseTrait;
    public function getIndex()
    {
        // 从session中获取基本信息
        $account = $this->getSessionInfo('account');
        $major_id = $account['major_id'];
        $institute_id = $account['institute_id'];
        // 从cache中获取课程信息
        $course = $this->cacheMajorCourse($institute_id,$major_id);
        return $this->json(1,$course);
    }
    public function getDetail(){
        $id =  request()->input("id");
        return $this->json(1,Model\Course::find($id));
    }

}
