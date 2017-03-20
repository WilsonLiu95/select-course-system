<?php
namespace App\Http\Controllers\Wechat;
use App\Model;
use App\Model\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;



trait BaseTrait {
    //====================================start session的相关操作================================================
    public function getSessionInfo($which){
        // 		which 即session中存储的基本用户信息,目前包括 id,type两个
        $session_which = session()->get($which);
        if (isset($session_which)){
            // 确保多设备登陆时,数据一致
            $this->checkMultiDevice(session()->get('id'));
            return $session_which;
        }else{
            $this->initSession();
            $this->checkMultiDevice(session()->get('id'));
            return session()->get($which);
        }
    }
    private function checkMultiDevice($student_id){ // 检测多台设备同时登陆
        $id = Session::getId();
        $key = "current_sid_" . $student_id; // 拼接cache中的键值
        $cache_id = Cache::tags(['select-course-sid'])
            ->rememberForever($key, function() use($id){
            return $id;
        });
        if($cache_id !== $id){ // 两者不相同则用户通过多台设备登陆,需保障数据一致性,故清除之前session内缓存的其他数据
            Cache::forget($cache_id);
            Cache::tags(['select-course-sid'])
                ->forever($key, $id);
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
            session()->put('id',$student->id);
            session()->put("isAbleHandleSelect",true); // 默认可以进行选课操作,同时只能进行一次选课操作
            session()->put("has_select_common_course",$has_select_common_course);
            session()->put("has_select_direction_course",$has_select_direction_course);
            session()->put('account',$student->account());

        }
    }
    //====================================end session的相关操作================================================


}