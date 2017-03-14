<?php

namespace App\Http\Controllers\Wechat;

use App\Jobs\QueueOneCourse;
use App\Model\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Wechat\BaseTrait;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class DirectionCourse extends Controller
{
    use BaseTrait;
    private $account;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认获取
        $this->account = $this->getSessionInfo('account');
    }

    public function getCanSelectCourse(){
        // 获取可以选修的选修课接口
        $courseList = $this->cacheDirection($this->account['institute_id'],$this->account['direction_id']);
        $current_number = $this->cacheDirStudentNum($this->account['institute_id'],$this->account['direction_id']);
        $data = [
            'current_number' => $current_number,
            'has_select_direction_course'=>$this->getSessionInfo('has_select_direction_course'),
            'has_select_common_course'=>$this->getSessionInfo('has_select_common_course'),
            'courseList'=>$courseList,
        ];
        return $this->json($data);
    }
    public function postHandleCourse(){
         if(!$this->getSessionInfo('isAbleSelect')){
             return $this->errorMsg('正在操作中,请勿重复提交选课'); // 同一时间,用户只能进行一次选课
         }

        // 选择课程的接口
        $queue_course = request()->course_id_arr;
        // 首先进行过滤,避免用户伪造接口请求数据
        $queue_course = $this->filterCanQueueCourse($queue_course);


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
        return $this->json(['queue_success'=>true]);
    }
    private function filterCanQueueCourse($queue_course){
        // 将已选课程以及不可选的课程排除在外
        $has_select_direction_course = collect($this->getSessionInfo('has_select_direction_course'));
        $direction_course = collect($this->cacheDirection($this->account['institute_id'], $this->account['direction_id']));
        $filtered = collect($queue_course)->filter(function($item)use($has_select_direction_course, $direction_course){
            // 首先,不允许包含已选中的课程
            if($has_select_direction_course->contains($item)){
                return false;
            }
            // 其次课程需要在与专业方向一致。理论上,不应该出现如此情况,以防万一(用户乱伪造请求)
            if(!$direction_course->contains("id", $item)){
                return false;
            }
            return true;
        });
        return $filtered->all();
    }
    public function getSelectResult(){
        $queue_course = $this->getSessionInfo('queue_course');
        $data = [];
        $success_select=[];
        // 进行判断,选课是否完成
        foreach ($queue_course as $course_id) {
            $one_result = $this->cacheSelectResult($this->account['institute_id'], $this->account['id'], $course_id);

            $data[$course_id] = $one_result['isSelect']; // 是否被选中

            if($one_result['isSelect']){ // 收集选课成功的课程
                $success_select[] = $course_id;
            }

            if(!$one_result['isFinish']){
                // 如果有课程没有结束,则直接返回
                return $this->json(['isFinish'=>false]);
            }
        }

        Cache::tags('student_'.$this->account['id'])->flush();// 清除cache中的所有的课程结果

        // 对session进行操作
        $has_select = $this->getSessionInfo('has_select_direction_course');
        session()->forget("queue_course"); // 删除队列信息
        session()->put('isAbleSelect', true); // 重置为可进行选课操作
        session()->put('has_select_direction_course', array_merge($has_select, $success_select)); // 更新session中,用户选中的课程
        return $this->json($data);
    }

    public function postQuitSelectCourse(){
        // 退选选修课的接口
        $data = request()->course_id_arr;


    }


}
