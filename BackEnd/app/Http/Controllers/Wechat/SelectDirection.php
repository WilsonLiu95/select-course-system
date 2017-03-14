<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SelectDirection extends Controller
{
    use BaseTrait;
    private $account;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->account = $this->getSessionInfo('account');

    }

    public function getCanSelectDir(){
        $res = [
            'isCanChangeDir'=>true,
            'data'=>[]
        ];
        if(!$this->isCanChangeDir()){
            // 首先判断选修课程是否已全退选
            return $this->json(['isCanChangeDir'=>false,
                'msg'=>'重选方向需退选其他课程,请手动退选所有选修课程',]);
        }
        $res['data'] = $this->userCanSelectDir();
        if(is_null($res['data'] )){
            return $this->errorMsg("系统出错,暂无你可选择的方向");
        }
        return $this->json($res);
    }

    public function postSelectDir(){
        if(!$this->isCanChangeDir()){
            // 首先判断选修课程是否已全退选
            return $this->json(['isCanChangeDir'=>false,'msg'=>'重选方向需退选其他课程,请手动退选所有选修课程']);
        }
        // 数据校验无误
        $this->validate(request(),[
            "direction_id" =>'required|integer']);

        $direction_id = request()->get('direction_id');

        $filterDir = $this->userCanSelectDir()->where('id',$direction_id);

        if($direction_id == $this->account['direction_id']){
            return $this->redirect(['name'=>"direction-course"],'重选的方向与原方向一致,更改无效');
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
            return $this->redirect(['name'=> 'direction-course'],'选定方向成功,马上为您自动跳转');
        }

        return $this->errorMsg("该方向不在您的候选范围内,请确认无误。如有问题,请咨询老师。");
    }
    private function userCanSelectDir(){
        return $this->cacheMajorDirMap($this->account['institute_id'],$this->account['major_id']); // 取cache中的数据
    }
    private function isCanChangeDir(){
        return count($this->getSessionInfo('has_select_direction_course')) == 0 ? true : false;

//        return $this->redirect(["name"=>'direction-course'], '重选方向需退选其他课程,请手动退选所有选修课程')->send();
    }
}
