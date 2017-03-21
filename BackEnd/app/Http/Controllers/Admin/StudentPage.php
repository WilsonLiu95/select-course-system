<?php

namespace App\Http\Controllers\Admin;

use App\Model\Classes;
use App\Model\Direction;
use App\Model\Student;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CacheHandle;
class StudentPage extends Controller
{
    use CacheHandle;
    private $institute_id;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->institute_id = session()->get('institute_id');
    }

    public function getStudentInit(){
        $page = $this->makeStudentTable(request()->option);

        $page['data'] = array_map( function($item) {
            return $this->makeStudentItem($item);
        }, $page['data']);
        $data['student_list'] = $page;

        return $this->json($data);
    }
    private function makeStudentTable($option){
        request()->page = 10;
        $handle = Student::where('institute_id', $this->institute_id);
        if(!request()->sortBy){
            $handle->orderBy('id','desc');
        }
        $page = $handle->paginate(20)->toArray();
        return $page;
    }
    private function makeStudentItem($item){
        $direction_map = Direction::where('institute_id', $this->institute_id)
            ->lists('name', 'id')->toArray();
        $class_map = Classes::where('institute_id', $this->institute_id)
            ->lists('name', 'id')->toArray();
        $student_item = [
            'id'=>$item['id'],
            'name'=> $item['name'],
            'openid'=> $item['openid'] ? $item['openid']:'未注册',
            'class'=> $item['classes_id'] ? $class_map[$item['classes_id']]:"未选定班级",
            'job_num'=> $item['job_num'],
            'direction'=> $item['direction_id'] ? $direction_map[$item['direction_id']]:'未选定方向'
        ];
        return $student_item;
    }
    public function postStudentSubmit(){

    }
}
