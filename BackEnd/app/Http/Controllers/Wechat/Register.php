<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Controllers\CacheHandle;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model;
class Register extends Controller
{
    use BaseTrait,CacheHandle;
    public function postIndex(Request $request)
    {
        // 数据校验
        $this->validate($request,[
            'job_num'=> 'required|alpha_num',
            'name'=> 'required',
        ]);

        // 注册绑定账号
        $sid = $this->getSessionInfo("openid");

        // 是否存在该用户信息
        $student = Model\Student::where("job_num", $request->job_num)
            ->where("name", $request->name);
        if($student->count() == 1){ // 存在该账号且只有一个。不可能谁把一个学生导两遍吧。导入时需校验唯一
            if(!$student->where('openid','')->exists()){ // openid需要为空,以避免别人冒充挤掉之前的人的绑定
                return $this->errorMsg('此账户已绑定,不允许再绑定,如有需要请联系教务科');
            }
            // 允许绑定账号,则更新openid
            $student->update(["openid" => $sid]);
        } else {
            return $this->errorMsg("不存在该账户,请确认姓名与学号");
        }
        // 注入session
        session()->put("id",$student->value("id"));
        if($this->getSessionInfo('account')['classes_id'] == 0) { // 如果classes_id为未设定过,则重定向到选择班级页面
            return $this->redirect(['name' => 'select-class'],'绑定账号成功,请先选择班级');
        }
        return $this->redirect([ "name" => 'select-class'],"绑定账号成功，自动为您跳转");
    }
    public function getIsLogin(){
        if($this->getSessionInfo("id")){ // id号码存在表示注册过
            if($this->getSessionInfo('account')['classes_id'] == 0) { // 如果classes_id为未设定过,则重定向到选择班级页面
                return $this->redirect(['name' => 'select-class']);
            }
            return $this->redirect([ "name" => 'tab-course']);
        }
    }
}
