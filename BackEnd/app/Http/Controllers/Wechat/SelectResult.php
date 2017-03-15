<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SelectResult extends Controller
{
    use BaseTrait;
    private $account;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->account = $this->getSessionInfo('account');

    }
    public function getIndex(){
        $has_select_common_course = $this->getSessionInfo('has_select_common_course');
        $has_select_direction_course = $this->getSessionInfo('has_select_direction_course');
        $major_course = $this->cacheMajorCourse($this->account['institute_id'], $this->account['major_id']);
        $major_course[0]['course']
    }
}
