<?php

namespace App\Http\Controllers\Admin;

use App\Model\Institute;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CacheHandle;
class Config extends Controller
{
    use CacheHandle;
    private $institute_id;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->institute_id = session()->get('institute_id');
    }
    public function getIndex(){
        $data['config'] = Institute::find($this->institute_id)->systemConfig()
            ->select('is_common_open','min_common_credit', 'max_common_credit',
                'is_direction_open','min_direction_credit','max_direction_credit')
            ->first();
        return $this->json($data);
    }
    public function postEdit(){
        $this->validate(request(),[
            'is_common_open'=> 'required|bool',
            'is_direction_open'=> 'required|bool',
            'min_common_credit'=> 'required|integer',
            'max_common_credit'=> 'required|integer',
            'min_direction_credit'=> 'required|integer',
            'max_direction_credit'=> 'required|integer',
        ]);
        if(request()->min_common_credit > request()->max_common_credit){
            return $this->errorMsg('公选课最低学分不能高于最高学分');
        }
        if(request()->min_direction_credit > request()->min_common_credit){
            return $this->errorMsg('公选课最低学分不能高于最高学分');
        }
        $newConfig = request()->only('is_common_open','min_common_credit', 'max_common_credit',
            'is_direction_open','min_direction_credit','max_direction_credit');
        $updateResult = Institute::find($this->institute_id)->systemConfig()->update($newConfig);

        if($updateResult){
            $this->cacheSystemConfig($this->institute_id, true);
            return $this->json(['msg'=>'更新成功']);
        }else{
            return $this->errorMsg('更新失败,请重试');
        }

    }
}
