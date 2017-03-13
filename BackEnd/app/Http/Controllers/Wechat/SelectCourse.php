<?php

namespace App\Http\Controllers\Wechat;

use App\Jobs\QueueOneCourse;
use App\Model\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Wechat\BaseTrait;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SelectCourse extends Controller
{
    use BaseTrait;
    private $account;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->account = $this->getSessionInfo('account');
    }

    public function getCanSelectCourse(){
        // 获取可以选修的选修课接口
        $courseList = $this->cacheDirection($this->account['institute_id'],$this->account['direction_id']);

        $data = [
            'current_number' => $this->cacheDirStudentNum($this->account['institute_id'],$this->account['direction_id']),
            'has_select_direction_course'=>$this->getSessionInfo('has_select_direction_course'),
            'has_select_common_course'=>$this->getSessionInfo('has_select_common_course'),
            'courseList'=>$courseList,
        ];
        return $this->json($data);
    }
    public function postSelectCourse(){

         if(!$this->getSessionInfo('isAbleSelect')){
             return $this->errorMsg('正在操作中,请勿重复提交选课'); // 同一时间,用户只能进行一次选课
         }

        // 选择课程的接口
        $queue_course = request()->course_id_arr;
        // 首先进行过滤,避免用户伪造接口请求数据
        // 过滤规则是,用户发的请求中,只能包含

        session()->put('isAbleSelect',false); // 阻止用户选课
        session()->put('queue_course', $queue_course); // 把本次丢入队列的课程存储到session中去

        $option = [ // 赋值
           'institute_id'=>$this->account['institute_id'],
           'grade_id'=>$this->account['grade_id'],
           'direction_id'=>$this->account['direction_id'],
           'student_id'=>$this->account['id'],
           'isQuit'=>false,
        ];
        foreach ($queue_course as $item) {
            $option['course_id'] = $item;
            // 缓存中标记该次选课
            $this->cacheSelectResult($option['institute_id'],$option['student_id'],$option['course_id']);
            // 分发选课到队列中去
            $this->dispatch((new QueueOneCourse($option,false)));
        }
        return $this->json('分发成功');
    }
    public function getSelectResult(){
        $queue_course = $this->getSessionInfo('queue_course');
        $data = [];
        foreach ($queue_course as $course_id) {
            $one_result = $this->cacheSelectResult($this->account['institute_id'], $this->account['id'], $course_id);

            $data[$course_id] = $one_result['isSelect']; // 是否被选中

            if(!$one_result['isFinish']){
                // 如果有课程没有结束,则直接返回
                return $this->json(['isFinish'=>false]);
            }
        }
        // 清除cache中的所有的课程结果
        \Cache::tags('student_'.$this->account['id'])->flush();
        session()->forget("queue_course"); // 删除队列信息
        session()->put('isAbleSelect', true); // 重置为可进行选课操作

        // 忘记用户已选的课程
        $has_select = $this->getSessionInfo('has_select_direction_course');

        session()->put('has_select_direction_course', array_merge($has_select, $queue_course));
        return $this->json($data);
    }

    public function postQuitSelectCourse(){
        // 退选选修课的接口
        $data = request()->course_id_arr;


    }


}
