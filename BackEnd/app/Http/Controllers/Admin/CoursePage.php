<?php

namespace App\Http\Controllers\Admin;

use App\Model\Course;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CacheHandle;


class CoursePage extends Controller
{
    use CacheHandle;
    private $institute_id;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->institute_id = session()->get('institute_id');
    }
    public function getCourse(){
        $course = $this->cacheMajorCourse($this->institute_id, 0);
        return $this->json($course);
    }
    public function getDelete(){
        $this->validate(request(),[
            'id'=>'required|integer',
        ]);
        Course::find(request()->id)->direction()->detach(); // 删除course_direction中课程关联数据
        Course::find(request()->id)->delete();
        $this->cacheFlush($this->institute_id);
    }
    public function postEdit(){
        $this->validate(request(),[
            'id'=>'required|integer',
            'title'=>'required',
            'is_common'=>'required|bool',
            'course_code'=>'required|numeric',
            'teacher'=>'required',
            'credit'=>'required|numeric',
            'required_number'=>'required|integer',
            'direction'=>'required|array',
            'detail'=>'required',
        ]);
        $newData = request()->only('title','is_common','course_code','teacher','credit','required_number','detail');
        $newData['institute_id'] = $this->institute_id; // 添加院系id
        $id = request()->id; // 课程ID,0表示新增
        if($id){
            $data['update'] = Course::find(request()->id)->update($newData);
        }else{
            $id = Course::create($newData)->id; // 新增之后,将id重新赋值
        }

        $direction_list = collect(request()->direction);
        $origin_list = Course::find($id)->direction()->lists('direction.id');

        $attach = $direction_list->diff($origin_list)->all();
        $detach = $origin_list->diff($direction_list)->all();
        $data['attach'] = Course::find($id)->direction()->attach($attach); // 添加新增的关联
        if($detach){ // 去除取消的关联
            Course::find($id)->direction()->detach($detach);
        }
        $this->cacheFlush($this->institute_id);
        return $this->json($data);
    }
}
