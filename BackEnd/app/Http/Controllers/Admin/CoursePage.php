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
        Course::find(request()->id)->direction()->detach();
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
            'credit'=>'required|integer',
            'required_number'=>'required|integer',
            'direction'=>'required|array',
            'detail'=>'required',
        ]);
        $newData = request()->only('title','is_common','course_code','teacher','credit','required_number','detail');
        Course::find($this->institute_id)->update($newData);
        Course::find($this->institute_id)->direction()->attach(request()->direction);
        $this->cacheFlush($this->institute_id);
    }
}
