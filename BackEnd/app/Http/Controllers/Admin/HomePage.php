<?php

namespace App\Http\Controllers\Admin;

use App\Model\Classes;
use App\Model\Course;
use App\Model\Direction;
use App\Model\Grade;
use App\Model\Institute;
use App\Model\Major;
use App\Model\SelectCourse;
use App\Model\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\CacheHandle;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class HomePage extends Controller
{
    use CacheHandle;
    private $institute_id;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->institute_id = session()->get('institute_id');
    }

    public function getConfig(){
        $data['config'] = Institute::find($this->institute_id)->systemConfig()
            ->select('is_common_open','min_common_credit', 'max_common_credit',
                'is_direction_open','min_direction_credit','max_direction_credit')
            ->first();
        return $this->json($data);
    }
    public function getCourse(){
        // 仅为查看当前学年的数据
        $now_grade = Grade::where('institute_id',$this->institute_id)->select('id','name')->first();
        if(!$now_grade){
            return $this->json(['msg'=>'当前仍未创建学年','now_grade'=>false]);
        }else{
            $data['now_grade'] = $now_grade; // 当前有正在运行的学年
        }
        $option = \GuzzleHttp\json_decode(request()->input('option'),true); // orderBy本处默认只支出当前选中人数排序
        $option['where'] = [
            ['institute_id','=',$this->institute_id], // 限制为自己学院
        ];
        $orderBy = array_only($option, 'orderBy')['orderBy'];
        $option = array_except($option,'orderBy');
        $course_list = $this->makePageHandle(Course::class, $option)->get()->toArray();
        $course = array_map(function($item){
            $selects = SelectCourse::where('course_id', $item['id'])
                ->where('isQuit', false)
                ->get()->toArray();
            $item['name_list'] = [];
            $item['current_num'] = $this->cacheSelectCourseNum($this->institute_id,$item['id']);
            foreach($selects as $select){
                $name = Student::where('id',$select['student_id'])->value('name');
                if($name){
                    $item['name_list'][] = $name;
                }
            }
            return $item;
        },$course_list);
        if($orderBy['order'] == 'desc'){ // 手动创建分页 支持按照选课人数排序
            $course = collect($course)->sortByDesc('current_num')->all();
        }else {
            $course = collect($course)->sortBy('current_num')->all();
        }
        $page = $option['page'];
        $size = $option['size'];
        $slice = array_slice($course, $size * ($page - 1), $size);
        $data['course'] = (new LengthAwarePaginator($slice, count($course), $size, $page))->toArray();

        return $this->json($data);
    }

    public function postEdit(){
        $this->validate(request(),[
            'is_common_open'=> 'required|bool',
            'is_direction_open'=> 'required|bool',
            'min_common_credit'=> 'required|integer',
            'max_common_credit'=> 'required|integer',
            'min_direction_credit'=> 'required|integer',
            'max_direction_credit'=> 'required|integer',
        ]);
        if(request()->min_common_credit > request()->max_common_credit){
            return $this->errorMsg('公选课最低学分不能高于最高学分');
        }
        if(request()->min_direction_credit > request()->min_direction_credit){
            return $this->errorMsg('专业方向选修课的最低学分不能高于最高学分');
        }
        $newConfig = request()->only('is_common_open','min_common_credit', 'max_common_credit',
            'is_direction_open','min_direction_credit','max_direction_credit');
        $updateResult = Institute::find($this->institute_id)->systemConfig()->update($newConfig);

        if($updateResult){
            $this->cacheSystemConfig($this->institute_id, true);
            $this->cacheFlush($this->institute_id);
            return $this->json(['msg'=>'更新成功']);
        }else{
            return $this->errorMsg('更新失败,请重试');
        }
    }
    public function postDelete(){
        $now_grade = Grade::where('institute_id',$this->institute_id)->select('id','name')->first();
        if(!$now_grade){
            return $this->errorMsg('当前未存在该系统');
        }
        Student::where('institute_id',$this->institute_id)
            ->where('grade_id', $now_grade['id'])->delete();
        SelectCourse::where('institute_id',$this->institute_id)
            ->where('grade_id', $now_grade['id'])->delete();
        Classes::where('institute_id',$this->institute_id)
            ->where('grade_id', $now_grade['id'])->delete();
        Grade::find($now_grade['id'])->delete();
        return $this->json(['msg'=>'删除成功']);
    }
    public function postAddNewYear(){
        $name = request()->grade_name;
        Grade::create([
            'name'=>$name,
            'institute_id'=>$this->institute_id
        ]);
        return $this->json(['msg'=>'创建成功']);
    }
}
