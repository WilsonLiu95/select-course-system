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

        $user = $this->getUser();
        $dir = $user->major->direction;
        $all_dir = $user->institute->direction;
        $data = [
            'course' => [],
            'direction' => ['公选课'],
            'all_direction'=>['公选课']
        ];


        // 公共课程排第一位
        $data['course'][0] = Model\Course::where("institute_id",1)
            ->where('is_common',true)->get()->toArray();
        foreach( $all_dir as $i=>$v){
            $data['all_direction'][$i+1] = $v->name;
        }
        foreach ($dir as $index => $value){
            $c = Model\Course::where("institute_id",1)
                ->where('direction_id',$value->id)->get()->toArray();
            $data['course'][$index+1] =$c;
            $data['direction'][$index+1] = $value->name;


        }
        return $this->json(1,$data);
    }

}
