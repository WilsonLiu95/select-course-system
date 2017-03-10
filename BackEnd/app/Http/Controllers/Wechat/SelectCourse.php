<?php

namespace App\Http\Controllers\Wechat;

use App\Model\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SelectCourse extends Controller
{
    use BaseTrait;
    private $account;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->account = $this->getSessionInfo('account');
    }
    public function getCanSelectCourse(){

        $cache_dir = $this->cacheDirection($this->account['institute_id'],$this->account['direction_id']);

        $data = [
            'current_number' => $this->cacheDirStudentNum($this->account['institute_id'],$this->account['direction_id']),

            'courseList'=>$cache_dir['courseList'],
        ];

        return $this->json($data);
    }
    public function postSelectCourse(){

    }
}
