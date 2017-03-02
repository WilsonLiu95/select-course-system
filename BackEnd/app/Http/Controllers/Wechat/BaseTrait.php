<?php
namespace App\Http\Controllers\Wechat;
use App\Model;
use App\Model\Student;

trait BaseTrait {
    public function getSessionInfo($which){
        // 		which 即session中存储的基本用户信息,目前包括 id,type两个
        $session_which = session()->get($which);
        if (isset($session_which)){
            return $session_which;
        }
        else{
            $this->confirmUser();
            return session()->get($which);
        }
    }
    // 	根据openid设置type与id,确保type与id确实存在于session中
    private function confirmUser(){
        // openid一定存在,否则会被鉴权挡回去做微信认证
        $openid = session()->get("openid");

        $student = Student::where("openid",$openid);
        if ($student->exists()){
            session()->put("id",$student->value('id'));
        }
    }
    public function getUser(){
        // 		查找user对象
        $id = $this->getSessionInfo("id");
        return Student::find($id);
    }




}