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
        if(is_null($course)){
           return $this->errorMsg("系统出错,暂无你的课程");
        }
        return $this->json($course);
    }
}
