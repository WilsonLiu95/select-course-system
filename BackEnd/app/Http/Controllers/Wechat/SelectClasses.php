<?php

namespace App\Http\Controllers\Wechat;

use App\Model\Classes;
use App\Model\Student;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class SelectClasses extends Controller
{
    use BaseTrait;
    private $account;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->account = $this->getSessionInfo('account');
    }

    public function getCanSelectClass(){
        if($this->isHasSelectClass()){
            return $this->redirect(['name'=> 'tab-account'],'班级已选定,将为您跳转。');
        }
        // 获取学院下所有可选的班级

        $data = Classes::where('institute_id', $this->account['institute_id'])
            ->where('grade_id',$this->account['grade_id']) // 加上年份
            ->get();
        return $this->json($data);
    }

    // 选择班级
    public function postSelectClass(){
        if($this->isHasSelectClass()){
            return $this->redirect(['name'=> 'tab-account'],'班级已选定,不可再次更改,如果问题请联系教务科。');
        }
        $this->validate(request(),['class_code'=>'required']);

        // 这里传class_code是为了限制学生只能选择他们学院的课程,而不能通过乱输入class_id来选定其他学院的班级
        $class_code = request()->get('class_code');

        $class = Classes::where('institute_id', $this->account['institute_id'])
            ->where('grade_id',$this->account['grade_id']) // 加上年份
            ->where('classes_code',$class_code)->first();
        if($class){ // 班级存在
            $data=[
                'classes_code'=> $class_code,
                'classes_id' => $class["id"]];
            if($class["major_code"]){
                // 如果班级的 major_code不为0,即自动帮学生选定专业
                // 否则,代表是不属于任何专业的特殊班级
                $data['major_code'] = $class["major_code"];
                $data['major_id'] = $class["major_id"];
            }

            Student::find($this->account['id'])->update($data);
            Session::forget('account');
            return $this->redirect(['name'=> 'tab-account'],'选定班级成功,正在为您自动跳转');
        }else{
            return $this->errorMsg('系统出错,暂无该班级,请检查');
        }
    }
    private function isHasSelectClass(){
        if($this->account['classes_id']){
            // 已经选定过班级,不可再更改
            return true;
        }
        return false;
    }
}
