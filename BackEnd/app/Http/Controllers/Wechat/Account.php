<?php

namespace App\Http\Controllers\Wechat;

use App\Model;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class Account extends Controller
{
    use BaseTrait;
    public function getIndex()
    {
        $data['account'] = $this->getSessionInfo('account');
        return $this->json(1, $data);
    }
    public function getCanSelectDir(){
        return $this->json(1,$this->userCanSelectDir());
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
        $data = $this->getUser()->institute->classes;
        return $this->json(1,$data);
    }
    public function postSelectClass(){
        // 说明没有录入过班级需要手动选择
        $class_code = request()->get('class_code');
        $user = $this->getUser();

        $class = $user->institute->classes()
            ->where('classes_code',$class_code);

        if($class->count()){ // 班级存在
            $_class = $class->first();
            $data=[
                'classes_code'=> $class_code,
                'classes_id' => $class->first()->id,
            ];
            if($_class->major_code){
                // 如果班级的 major_code不为0,即自动帮学生选定专业
                // 否则,代表是可以自选专业的班级
                $data['major_code'] = $_class->major_code;
                $data['major_id'] = $_class->major_id;
            }
            $user->update($data);
            return $this->redirect(['name'=> 'start-select'],'选定反向成功,马上为您自动跳转');
        }else{
            return $this->toast(0,'系统出错,暂无该班级,请检查');
        }
    }

    private function userCanSelectDir(){
        $account = $this->getSessionInfo("account");
        return $this->cacheMajorDirMap($account['institute_id'],$account['major_id']); // 取cache中的数据
    }
}
