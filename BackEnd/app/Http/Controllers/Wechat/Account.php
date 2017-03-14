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
        $data['has_select_direction_course'] = $this->getSessionInfo('has_select_direction_course');
        $data['system_status'] = $this->cacheSystemConfig($this->account['institute_id'])['system_status'];

        return $this->json($data);
    }







}
