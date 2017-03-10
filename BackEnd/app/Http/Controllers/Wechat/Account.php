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
        $this->account = $this->getSessionInfo('account');
    }
    public function getIndex()
    {
        $data['account'] = $this->account;
        return $this->json($data);
    }
    public function getCanSelectDir(){
        return $this->json($this->userCanSelectDir());
    }
    public function postSelectDir(){
        // 数据校验无误
        $this->validate(request(),[
            "direction_id" =>'required|integer']);
        $direction_id = request()->get('direction_id');

        if($this->userCanSelectDir()->where('id',$direction_id)->isEmpty()){
            // 用户有权选择该方向
            $this->getUser()->update([
                'direction_id' => $direction_id ,
            ]);
            session()->forget('account'); // 更新账户数据
            return $this->redirect(['name'=> 'select-course'],'选定方向成功,马上为您自动跳转');
        }
        return $this->toast("该方向不在您的候选范围内,请确认无误。如有问题,请咨询老师。");

    }
    public function getCanSelectClass(){
        if($this->isHasSelectClass()){
            return $this->redirect(['name'=> 'start-select']);
        }
        // 获取学院下所有可选的班级
        $data = Model\Classes::where('institute_id', $this->account['institute_id'])->get();
        return $this->json($data);
    }
    public function postSelectClass(){
        if($this->isHasSelectClass()){
            return $this->redirect(['name'=> 'start-select']);
        }
        // 说明没有录入过班级需要手动选择
        $class_code = request()->get('class_code');

        $class = Model\Classes::where('institute_id', $this->account['institute_id'])
            ->where('classes_code',$class_code)->first();

        if($class->count()){ // 班级存在
            $data=[
                'classes_code'=> $class_code,
                'classes_id' => $class->id,
            ];
            if(!$class->major_code){
                // 如果班级的 major_code不为0,即自动帮学生选定专业
                // 否则,代表是不属于任何专业的特殊班级
                $data['major_code'] = $class->major_code;
                $data['major_id'] = $class->major_id;
            }
            $this->getUser()->update($data);
            Session::forget('account');
            return $this->redirect(['name'=> 'start-select'],'选定反向成功,马上为您自动跳转');
        }else{
            return $this->toast('系统出错,暂无该班级,请检查');
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
