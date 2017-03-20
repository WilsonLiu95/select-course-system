<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CacheHandle;
class Info extends Controller
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

        $data['major'] = Institute::find($this->institute_id)->major()->get();
        $data['classes'] = Institute::find($this->institute_id)->classes()->with('major')->get();
        $data['direction'] = Institute::find($this->institute_id)->direction()->with('major')->get();
        return $this->json($data);
    }
}
