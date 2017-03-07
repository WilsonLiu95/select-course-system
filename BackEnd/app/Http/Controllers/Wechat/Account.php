<?php

namespace App\Http\Controllers\Wechat;

use App\Model;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Account extends Controller
{
    use BaseTrait;
    public function getIndex()
    {
        $data['account'] = $this->getUser()->account();
        return $this->json(1,$data);
    }

    public function getCanSelectDir(){
        $data = $this->getUser()->major->direction;
        return $this->json(1,$data);
    }
    public function postSelectDir(){
        $direction_code = request()->get('direction_code');
        $user = $this->getUser();
        $dir = $user->major->direction()
            ->where('direction_code',$direction_code);
        if($dir->count()){
            $user->update([
                'direction_id' => $dir->first()->id,
            ]);
            return $this->redirect(['name'=> 'select-course'],'选定反向成功,马上为您自动跳转');
        }else{
            return $this->toast(0,'系统出错,暂无该课程,请检查');
        }
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
}
