<?php
namespace App\Http\Controllers\Wechat;
use App\Model;
use App\Model\Student;
use Illuminate\Support\Facades\Cache;


trait BaseTrait {
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
    public function cacheMajorDirMap($institute_id,$major_id){
        // 基本长期不变的数据
        $key = 'major_dir_map_'. $institute_id;
        return Cache::get($key, function()use($institute_id, $key){
            $data = [];
            $ins = Model\Institute::find($institute_id);
            $majorList = $ins->major;
            $data[0] = $ins->direction()
                ->select('direction.id','direction.name')->get();
            foreach($majorList as $major){
                $data[$major->id] = $major->direction()
                    ->select('direction.id','direction.name')->get();
            }
            Cache::put($key, $data, 2); // 1分钟过期时间
            return $data;
        })[$major_id];
    }
    public function cacheDirection($institute_id,$direction_id){
        // 方向数据不敏感,以DB的方向数据为准,每6S自动更新一次
        $key = "direction_". $institute_id . "_" . $direction_id;
        return Cache::get($key, function()use($institute_id,$direction_id,$key){
            $data = array();
            $data['currentSelectNum'] = Student::where('institute_id',$institute_id)
                ->where('direction_id',$direction_id)->count();;
            $course = Model\Direction::find($direction_id)->course();
            $data['courseMap'] = $course->lists('course.id'); // 存储该专业方向对应的所有课程id
            $data['courseList'] =  $course->select("course.id","title","teacher","credit",'required_number')->get();
            Cache::put($key, $data, 0.1);
            return $data;
        });
    }
    public function cacheMajorCourse($institute_id, $major_id){
        // 专业所允许的所有课程数据, 包括 选修课与公共课程
        $key = 'major_course_' . $institute_id . "_". $major_id;
        return Cache::get($key, function()use($institute_id, $major_id,$key){
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
            Cache::put($key, $data, 2);
            return $data;

        });
    }
    // 	根据openid设置type与id,确保type与id确实存在于session中
    private function initSession(){
        // openid一定存在,否则会被鉴权挡回去做微信认证
        $openid = session()->get("openid");

        $student = Student::where("openid",$openid);
        if ($student->exists()){
            session()->put("id",$student->value('id'));
            session()->put('account',$student->first()->account());

        }
    }






}