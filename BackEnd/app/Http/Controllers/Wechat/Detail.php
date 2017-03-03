<?php

namespace App\Http\Controllers\Wechat;



use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Course;

class Detail extends Controller
{
    use BaseTrait;
    public function getIndex()
    {
        $id =  request()->input("id");
        return $this->json(1,Course::find($id));
    }

    public function getSelectCourse(){


    }

    public function getCancelSelectCourse(){

    }
    public function postCheckCourse(){

    }


    private function isMaxSelectCourse(){
        $num = $this->getUser()->schedule()->whereIn('status',[1,2])->count();
        $max = $this->getGrade()->max_select_class;
        return $num < $max ? false: true;
    }

}
