<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Login extends Controller
{
	public function postIndex()
	{
		$account = request()->input("account");
		$password = request()->input("password");
		$admin = Admin::where("account",$account)->where("password",$password);
		if($admin->count()){
			session()->flush();
			session()->set("isLogin",true);
			session()->set("institute_id",true);
			return $this->redirect(['name'=>"home"],'登陆成功');
		}
		return $this->toast(0,"账号密码错误,请重试");
	}
	public function getIsLogin(){
		if(session()->get('isLogin')){
			return $this->redirect(['name'=>'home'],'已登录,正在为您自动跳转');
		}
	}
}
