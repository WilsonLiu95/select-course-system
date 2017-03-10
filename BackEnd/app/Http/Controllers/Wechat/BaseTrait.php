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
        }
        else{
            $this->initSession();
            return session()->get($which);
        }
    }

    // 	根据openid设置type与id,确保type与id确实存在于session中
    private function initSession(){
        // openid一定存在,否则会被鉴权挡回去做微信认证
        $openid = session()->get("openid");
        $student = Student::where("openid",$openid);
        if ($student->exists()){
            session()->put('account',$student->first()->account());

        }
    }
    //====================================end session的相关操作================================================


    //====================================start cache的相关操作================================================

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
                $data = array();
                $course = Model\Direction::find($direction_id)->course();
                $data['courseMap'] = $course->lists('course.id'); // 存储该专业方向对应的所有课程id
                $data['courseList'] =  $course->select("course.id","title","teacher","credit",'required_number')->get();
                return $data;
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
                    $data[$index+1]['course']=$this->cacheDirection($institute_id, $item->id)['courseList'];
                }
                return $data;
        });
    }

    public function cacheDirStudentNum($institute_id,$direction_id){ // 某个方向有多少人
        $key = 'direction_student_num_' .$direction_id;
        if(!Cache::has($key)){
            // 如果不存在,则先进行查询,并存储
             Cache::tags(["ins" . $institute_id])
                 ->remember($key, 1,function()use($institute_id, $direction_id){
                    return Student::where('institute_id',$institute_id)
                        ->where('direction_id',$direction_id)->count();
                });
            }
            return Cache::get($key);
    }
    public function cacheHandleDir($institute_id,$direction_id, $isSelect){ // 增减方向的人数
        $key = 'direction_student_num_' . $direction_id;
        $this->cacheDirection(); // 先运行一遍,确保存在
        $isSelect ? Cache::tags([ins.$institute_id])->increment($key) :
            Cache::tags([ins.$institute_id])->decrement($key);
    }

    public function cacheSelectCourse($institute_id, $direction_id, $course_id, $student_id){
        // 选课

    }
    public function cacheQuitCourse($institute_id, $direction_id, $course_id, $student_id){
        // 退选

    }
    //====================================end cache操作================================================











}