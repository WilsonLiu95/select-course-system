<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model;
class Register extends Controller
{
    use BaseTrait;
    public function postIndex(Request $request)
    {
        $sid = session()->get("openid");

        // 是否已经绑定过
        $hasRegisterStudent = Model\Student::where('openid',$sid);

        // 是否存在该用户信息
        $isExistStudent = Model\Student::where("job_num", $request->job_num)->where("name", $request->name);

        if($hasRegisterStudent->exists()){
            // 如果已注册绑定过

            $user = $hasRegisterStudent->first() ;

            $msg = '您已经注册过,请勿重复注册,即将为您跳转';
        }else if($isExistStudent->exists()){


            $user = $isExistStudent->first();
            // 如果存在,则更新openid
            $user->update([
                "openid" => $sid]);
            $msg = "登录成功，自动为您跳转";
        }else{
            return $this->toast(0,"不存在该账户,请确认姓名与学号");
        }
        // 注入session
        session()->put("isLogin", true);
        session()->put("id",$user["id"]);

        if(!$this->getUser()->classes_id) { // 如果classes_id为空,则先导向课程
            return $this->redirect(['name' => 'select-class']);
        }
        return $this->redirect([ "name" => 'select-class'],$msg);
    }
    public function getIsLogin(){
        if($this->getSessionInfo("isLogin")){
            if(!$this->getUser()->classes_id) { // 如果classes_id为空,则先导向课程
                return $this->redirect(['name' => 'select-class']);
            }

            return $this->redirect([ "name" => 'course'],'已登录，为您自动跳转');
        }else{
            return $this->toast(0,"请先注册");
        }

    }
}
