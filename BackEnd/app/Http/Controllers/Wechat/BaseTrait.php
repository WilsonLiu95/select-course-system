<?php
namespace App\Http\Controllers\Wechat;
use App\Model;
use App\Model\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use phpDocumentor\Reflection\Types\Boolean;


trait BaseTrait {


    //====================================start session的相关操作================================================
    public function getUser(){
        // 		查找user对象
        $id = $this->getSessionInfo("id");
        return Student::find($id);
    }

    public function getSessionInfo($which){
        // 		which 即session中存储的基本用户信息,目前包括 id,type两个
        $session_which = session()->get($which);
        if (isset($session_which)){
            return $session_which;
        }else{
            $this->initSession();
            return session()->get($which);
        }
    }

    // 	根据openid设置type与id,确保type与id确实存在于session中
    private function initSession(){
        // openid一定存在,否则会被鉴权挡回去做微信认证
        $openid = session()->get("openid");
        $student = Student::where("openid",$openid)->first();

        $has_select_common_course = Model\SelectCourse::where('student_id',$student['id'])
            ->where('isQuit',false)
            ->where('direction_id', 0)->lists('course_id')->toArray();
        $has_select_direction_course = Model\SelectCourse::where('student_id',$student['id'])
            ->where('isQuit',false)
            ->where('direction_id',"!=", 0)->lists('course_id')->toArray();
        if ($student){
            session()->put("isAbleSelect",true); // 默认可以进行选课操作,同时只能进行一次选课操作
            session()->put("has_select_common_course",$has_select_common_course);
            session()->put("has_select_direction_course",$has_select_direction_course);
            session()->put('account',$student->account());

        }
    }
    //====================================end session的相关操作================================================


    //====================================start cache的相关操作================================================

    public function cacheSystemConfig($institute_id){
        $key = 'system_config' . $institute_id;
        return Cache::tags(["ins" . $institute_id])
            ->remember($key, 0.1, function()use($institute_id){
              return Model\Grade::where('institute_id',$institute_id)
                   ->select('system_status',"min_credit")->first()->toArray();
            });
    }
    public function cacheMajorDirMap($institute_id,$major_id){ // 专业与方向的映射表
        // 基本长期不变的数据
        $key = 'major_dir_map';
        return Cache::tags(["ins" . $institute_id]) // 加上标签
            ->remember($key, 1, function()use($institute_id){
                $data = [];
                $ins = Model\Institute::find($institute_id);
                $majorList = $ins->major;
                $data[0] = $ins->direction()
                    ->select('direction.id','direction.name')->get();
                foreach($majorList as $major){
                    $data[$major->id] = $major->direction()
                        ->select('direction.id','direction.name')->get();
                }
                return $data;
        })[$major_id];
    }
    public function cacheDirection($institute_id,$direction_id){ // 方向对于课程的映射表以及方向下课程的内容
        // 方向数据不敏感,以DB的方向数据为准,每6S自动更新一次
        $key = "direction_" . $direction_id;
        return Cache::tags(["ins" . $institute_id])
            ->remember($key, 0.1, function() use($institute_id,$direction_id){

                $course = Model\Direction::find($direction_id)->course();
                $courseList =  $course->select("course.id","title","teacher","credit",'required_number','detail')->get();

                // 去更新每个课程的人数
                $courseList->map(function ($item) use($institute_id) {
                    $item['current_number'] = $this->cacheSelectCourseNum($institute_id, $item["id"]);
                    return $item;
                });

                return $courseList;
        });
    }
    public function cacheMajorCourse($institute_id, $major_id){ // 缓存专业对应的课程
        // 专业所允许的所有课程数据, 包括 选修课与公共课程
        $key = 'major_course_' . $major_id;
        return Cache::tags(["ins" . $institute_id])
            ->remember($key, 1, function()use($institute_id, $major_id){
                $data = [];
                $common_course = Model\Course::where("institute_id",$institute_id)
                    ->where('is_common', true)->get();
                $data[0] = [
                  "id"=>0,
                  "name" => "公选课",
                  "course" => $common_course,
                ];
                $dirArrMap = $this->cacheMajorDirMap($institute_id,$major_id);
                foreach ($dirArrMap as $index => $item) {
                    $data[$index+1]['id'] = $item->id;
                    $data[$index+1]['name'] = $item->name;
                    $data[$index+1]['course']=$this->cacheDirection($institute_id, $item->id);
                }
                return $data;
        });
    }

    public function cacheDirStudentNum($institute_id, $direction_id){ // 某个方向有多少人
        $key = 'direction_student_num_' .$direction_id;
        return (int)Cache::tags(["ins" . $institute_id])
                 ->remember($key, 2, function()use($institute_id, $direction_id){
                    return Student::where('institute_id',$institute_id)
                        ->where('direction_id',$direction_id)->count();
                });
    }
    public function cacheHandleDir($institute_id,$direction_id, $isSelect){ // 增减方向的人数
        $key = 'direction_student_num_' . $direction_id;
        $this->cacheDirection($institute_id,$direction_id); // 先运行一遍,确保存在
        $isSelect ? Cache::tags(["ins" .$institute_id])->increment($key) :
            Cache::tags(['ins'.$institute_id])->decrement($key);
    }
    public function cacheRequiredNum($institute_id, $course_id){
        $key= "course_required_num_" . $course_id;

        return Cache::remember($key, 1, function()use($institute_id){
           return Model\Course::where("institute_id",$institute_id)
                ->lists('required_number',"id");
        })[$course_id];
    }
    public function cacheSelectCourseNum($institute_id, $course_id){
        // 已经选中该课程学生的数量
        $key = 'course_has_select_num_' . $course_id;

        return (int)Cache::tags(["ins" . $institute_id])
            ->remember($key, 2, function()use($institute_id, $course_id){ // 永远记着
               return Model\SelectCourse::where('institute_id',$institute_id)
                   ->where('course_id', $course_id)
                   ->where('isQuit',false)->count(); // 统计没有退选的人数
            });
    }

    public function cacheHandleSelectCourseNum($institute_id, $course_id, $isQuit){
        // 操作当前选中该课程的学生数量
        $key = 'course_has_select_num_' . $course_id;
        $this->cacheSelectCourseNum($institute_id, $course_id); // 确保存在
        $isQuit ? Cache::tags(["ins" . $institute_id])->decrement($key)
            :Cache::tags(["ins" . $institute_id])->increment($key);
    }

    // 课程等待人数的相关函数
    public function cacheWaitSelectCourseNum($institute_id, $course_id){
        // 队列中在选取该课程的数量
        $key = 'course_wait_select_num_' . $course_id;
        return (int)Cache::tags(["ins" . $institute_id])
            ->remember($key, 2, function()use($institute_id, $course_id){
                return 0; // 启动时,默认为0
                // TODO: 系统重启后,如何获取队列中剩下的任务个数
            });
    }
    public function cacheHandleWaitSelectCourseNum($institute_id, $course_id, $isHasBeenExecute){
        // 操作队列选课的数据
        $key = 'course_wait_select_num_' . $course_id;
        $nowWaitNum = $this->cacheSelectCourseNum($institute_id, $course_id); // 确保存在
        if($nowWaitNum == 0){
            // 担心,系统重启,导致本数据丢失,但是队列中任务仍然存在,重启后,相应任务继续执行,造成本数字为负
            return true;
        }else{
            // 否则对其进行加减操作
            $isHasBeenExecute ? Cache::tags(["ins" . $institute_id])->decrement($key)
                : Cache::tags(["ins" . $institute_id])->increment($key);
        }

    }

    // 调用以初始化各个选课的操作,以及每次查询选课进度
    public function cacheSelectResult($institute_id, $student_id, $course_id){
        $key = 'selectResult_' . $student_id . "_" . $course_id;
        return Cache::tags(["ins" . $institute_id, "student_" . $student_id])
            ->rememberForever($key,function(){ // 需要手动清除
                return ["isSuccess"=> false,// 是否选上该门课程
                        "isFinish"=> false, ];// 队列中该任务是否完成
            });
    }

    public function cacheHandleSelectResult($institute_id, $student_id, $course_id, $isSuccess, $isFinish){
        $key = 'selectResult_' . $student_id . "_" . $course_id;

        if(Cache::tags(["ins" . $institute_id, "student_" . $student_id])->has($key)){
            // 如果cache中存在该选课记录,则对其进行修改
            Cache::tags(["ins" . $institute_id, "student_" . $student_id])
                ->forever($key, [
                    "isSuccess"=> $isSuccess,// 是否选上该门课程
                    "isFinish"=> $isFinish, // 队列中该任务是否完成
                ]);
        }
    }
    //====================================end cache操作================================================

}