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
        $data['grade_map'] = Grade::withTrashed()->where('institute_id',$this->institute_id)
            ->lists('name','id')
            ;
        $data['current_grade'] = Institute::find($this->institute_id)->grade()->select('id','name')->first();
        return $this->json($data);
    }
    public function postCourse(){
        $option = request()->only('search', 'size', 'orderBy', 'page', 'filter');
        $option['where'] = [
            ['institute_id','=',$this->institute_id], // 限制为自己学院
        ];
        $grade_id = Institute::find($this->institute_id)->grade()->select('id','name')->first();
        $course = $this->makePage(Course::class, $option, true);//允许查看软删除的数据
        $course['data'] = array_map(function($item){
            $selects = SelectCourse::withTrashed()
                ->where('course_id', $item['id'])
                ->get()->toArray();
            $item['name_list'] = 'ee';
            foreach($selects as $select){
                $item['name_list'].=Student::withTrashed()->where('id',$select['id'])->value('name');
            }

            return $item;

        },$course['data']);

//            $item['test']= [];

//                foreach($selects as $select){

//                }
            // $item['test'] = $a;


        return $this->json($course);
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
}
