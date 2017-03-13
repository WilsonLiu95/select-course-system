<?php

namespace App\Http\Controllers\Wechat;

use App\Model;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class Account extends Controller
{
    use BaseTrait;
    private $account;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->account = $this->getSessionInfo('account');
    }
    public function getIndex()
    {
        $data['account'] = $this->account;
        return $this->json($data);
    }
    public function getCanSelectDir(){
        $dir = $this->userCanSelectDir();
        if(is_null($dir)){
            return $this->errorMsg("系统出错,暂无你可选择的方向");
        }
        return $this->json($dir);
    }
    public function postSelectDir(){
        // 数据校验无误
        $this->validate(request(),[
            "direction_id" =>'required|integer']);

        $direction_id = request()->get('direction_id');

        $filterDir = $this->userCanSelectDir()->where('id',$direction_id);

        if($direction_id == $this->account['direction_id']){
            return $this->redirect(['name'=>"select-course"],'重选的方向与原方向一致,更改无效');
        }
        if(!$filterDir->isEmpty()){
            // 不为空,用户有权选择该方向
            $isUpdate = $this->getUser()->update([ // 选定新的方向
                'direction_id' => $direction_id ,
            ]);
            if(!$isUpdate){
                // 如果操作失败,则直接返回error
                return $this->errorMsg('选定方向失败,请重试');
            }
            // 先行操作cache中方向的数据,如果之前有选定方向,则退选之前的方向先
            if($this->account['direction_id']){
                $this->cacheHandleDir($this->account['institute_id'], $this->account['direction_id'], false);
            }

            $this->cacheHandleDir($this->account['institute_id'], $direction_id, true);

            session()->forget('account'); // 更新账户数据
            return $this->redirect(['name'=> 'select-course'],'选定方向成功,马上为您自动跳转');
        }

        return $this->errorMsg("该方向不在您的候选范围内,请确认无误。如有问题,请咨询老师。");
    }


    public function getCanSelectClass(){
        if($this->isHasSelectClass()){
            return $this->redirect(['name'=> 'start-select'],'班级已选定,将为您跳转。');
        }
        // 获取学院下所有可选的班级
        $data = Model\Classes::where('institute_id', $this->account['institute_id'])
            ->where('grade_id',$this->account['grade_id']) // 加上年份
            ->get();
        return $this->json($data);
    }

    // 选择班级
    public function postSelectClass(){
        if($this->isHasSelectClass()){
            return $this->redirect(['name'=> 'start-select'],'班级已选定,不可再次更改,如果问题请联系教务科。');
        }
        $this->validate(request(),['class_code'=>'required']);

        // 这里传class_code是为了限制学生只能选择他们学院的课程,而不能通过乱输入class_id来选定其他学院的班级
        $class_code = request()->get('class_code');

        $class = Model\Classes::where('institute_id', $this->account['institute_id'])
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
            
            $this->getUser()->update($data);
            Session::forget('account');
            return $this->redirect(['name'=> 'start-select'],'选定班级成功,正在为您自动跳转');
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

    private function userCanSelectDir(){
        return $this->cacheMajorDirMap($this->account['institute_id'],$this->account['major_id']); // 取cache中的数据
    }
}
