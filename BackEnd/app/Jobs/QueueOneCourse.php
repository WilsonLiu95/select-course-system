<?php

namespace App\Jobs;

use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Controllers\Wechat\SelectCourse;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;

class QueueOneCourse extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    use BaseTrait;
    protected $option;
    protected $isQuit;
    /**
     * Create a new job instance.
     *
     * $option=[ // 赋值
     *  'institute_id'=>$institute_id,
     *  'grade_id'=>$grade_id,
     *  'direction_id'=>$direction_id,
     *  'course_id'=>$course_id,
     *  'student_id'=>$student_id,
     *  'isQuit'=>$isQuit
     *  ]
     * $isQuit 是否退选
     * @return void
     */
    public function __construct($option, $isQuit)
    {
        $this->option = $option;
        $this->isQuit = $isQuit;
        // 进入执行,则对cache减1
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 重试超过3次,直接丢弃
        if ($this->attempts() < 3) {
            // 简单的分发下选课与退选课程
            if(!$this->isQuit){
                $this->jobSelectOneCourse();
            }else{
                $this->jobQuitOneCourse();
            }
        }


    }
    private function jobSelectOneCourse(){
        // 进行判断,当前课程是否已经选满,获取该门课程需要的人数以及当前选中人数
        $required_num = $this->cacheRequiredNum($this->option['institute_id'],$this->option['course_id']);
        $current_num = $this->cacheSelectCourseNum($this->option['institute_id'],$this->option['course_id']);
        if($current_num >= $required_num) {
            // 课程已被选满,返回课程处理结果
            $this->cacheHandleSelectResult($this->option['institute_id'],$this->option['student_id'],$this->option['course_id'], false, true);
            return false; // 直接返回
        }
        // 课程未选满,继续选课,新增一条选课记录。理论上可以直接使用create,因为分发选课事件任务时,进行过检测筛选掉了已选课程
        \App\Model\SelectCourse::firstOrCreate($this->option);

        // cache中该课程的选中人数+1,队列等待人数-1
        $this->cacheHandleSelectCourseNum($this->option['institute_id'], $this->option['course_id'], true);
        $this->cacheHandleWaitSelectCourseNum($this->option['institute_id'], $this->option['course_id'], true);
        // 将处理结果反馈给cache中的select_result
        $this->cacheHandleSelectResult($this->option['institute_id'], $this->option['student_id'],$this->option['course_id'], true, true);
        return true;
    }

    private function jobQuitOneCourse(){

    }
}
