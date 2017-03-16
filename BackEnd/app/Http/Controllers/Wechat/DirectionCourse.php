<?php

namespace App\Http\Controllers\Wechat;

use App\Jobs\QueueOneCourse;
use App\Model\Course;
use Flexihash\Flexihash;
use Illuminate\Http\Request;
use App\Http\Controllers\Wechat\BaseTrait;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class DirectionCourse extends Controller
{
    use BaseTrait;
    private $account;
    private $hash;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认获取

        $this->hash= new Flexihash();
        // bulk add
        $this->hash->addTargets(array('default'));
        $this->account = $this->getSessionInfo('account');
    }
    public function getCanSelectCourse(){
        $data = $this->makeInitPageData($this->account['institute_id'], $this->account['direction_id']); // 公选课方向id为0
        return $this->json($data);
    }
    private function makeInitPageData($institute_id, $direction_id){
        // 获取可以选修的选修课接口
        $courseList = $this->cacheDirection($institute_id, $direction_id);
        // 去更新每个课程的人数
        $courseList->map(function ($item)  {
            $item['current_number'] = $this->cacheSelectCourseNum($this->account['institute_id'], $item["id"]);
            return $item;
        });
        $has_select_course = $direction_id == 0 ? 'has_select_common_course' : 'has_select_direction_course';
        $data = [
            'has_select_course'=>$this->getSessionInfo($has_select_course),
            'courseList'=>$courseList,
        ];
        return $data;
    }
    // 操作选课的接口
    public function postHandleCourse(){
         if(!$this->getSessionInfo('isAbleHandleSelect')){
             return $this->errorMsg('正在操作中,请勿重复提交选课'); // 同一时间,用户只能进行一次选课
         }
        $this->validate(request(),[ // 数据校验
            'course_id_arr'=> 'required|array' ,
            'isQuit'=> 'required|boolean',
        ]);
        // 选择课程的接口
        $queue_course = request()->course_id_arr;
        $isQuit = request()->isQuit;

        // 首先进行过滤,避免用户伪造接口请求数据
        $queue_course = $this->filterCanQueueCourse($queue_course, $isQuit);
        // 调用课程操作处理函数
        $this->handleSelectCourse($queue_course, $isQuit);
        return $this->json(['queue_success'=>true,'queue'=>$queue_course]);
    }
    // 私有的分发课程函数
    private function handleSelectCourse($queue_course,$isQuit){
        session()->put('isAbleHandleSelect',false); // 阻止用户选课
        session()->put('isQuit',$isQuit); // 记录用户是选课还是退选
        session()->put('queue_course', $queue_course); // 把本次丢入队列的课程存储到session中去

        $option = [ // 赋值
            'institute_id'=>$this->account['institute_id'],
            'grade_id'=>$this->account['grade_id'],
            'direction_id'=>$this->account['direction_id'],
            'student_id'=>$this->account['id'],
            'isQuit'=>$isQuit
        ];
        foreach ($queue_course as $course_id) {
            $option['course_id'] = $course_id;
            // 缓存中标记该次选课
            $this->cacheSelectResult($option['institute_id'],$option['student_id'],$option['course_id']);

            // 分发选课到队列中去
            $queue_name = $this->hash->lookup($course_id);
            $this->dispatch((new QueueOneCourse($option,$isQuit))->onQueue($queue_name));
        }
    }
    private function filterCanQueueCourse($queue_course, $isQuit){
        // 将已选课程以及不可选的课程排除在外
        $has_select_direction_course = collect($this->getSessionInfo('has_select_direction_course'));
        $direction_course = collect($this->cacheDirection($this->account['institute_id'], $this->account['direction_id']));
        $filtered = collect($queue_course)->filter(function($item)use($has_select_direction_course, $direction_course, $isQuit){
            // 首先课程需要在与专业方向一致。理论上,不应该出现如此情况,以防万一(用户乱伪造请求)
            if(!$direction_course->contains("id", $item)){
                return false;
            }
            // 其次,需要查看已选课程
            if($has_select_direction_course->contains($item)){
                return $isQuit ? true : false; // 如果是退选课程,则包含应该返回true
            }else{
                return $isQuit ? false : true; // 如果不包含,如果是选课则返回true,如果是推选课程,应该返回false
            }
        });
        return $filtered->all();
    }
    public function getSelectResult(){
        $queue_course = $this->getSessionInfo('queue_course');
        $data = [
            'data'=> [], // 用来传输新的课程数据
            'result'=>[], // 用来传输结果
            'isFinish'=>true
        ];
        $success_handle=[];
        // 进行判断,选课是否完成
        foreach ($queue_course as $course_id) {
            $one_result = $this->cacheSelectResult($this->account['institute_id'], $this->account['id'], $course_id);
            if(!$one_result['isFinish']){
                // 如果有课程没有结束,则直接返回。选课动画继续
                $data['isFinish']=false;
                return $this->json($data);
            }
            $data['result'][$course_id] = $one_result['isSuccess']; // 是否被选中
            if($one_result['isSuccess']){ // 收集选课成功的课程
                $success_handle[] = $course_id;
            }
        }
        // 如果所有选课操作都处理完成,则返回结果
        $this->cacheFogetResult($this->account['id']);

        // 对session进行操作
        $has_select = $this->getSessionInfo('has_select_direction_course');
        session()->forget("queue_course"); // 删除队列信息
        session()->put('isAbleHandleSelect', true); // 重置为可进行选课操作
        if(session()->get('isQuit')){
            // 退选课程
            session()->put('has_select_direction_course', array_values(array_diff($has_select, $success_handle))); // 更新session中,用户选中的课程
        }else{
            // 选课
            session()->put('has_select_direction_course', array_merge($has_select, $success_handle)); // 更新session中,用户选中的课程
        }
        $data['data'] = $this->makeInitPageData($this->account['institute_id'],$this->account['direction_id']);
        return $this->json($data);
    }



}
