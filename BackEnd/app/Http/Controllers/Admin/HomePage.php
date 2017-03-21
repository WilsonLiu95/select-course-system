<?php

namespace App\Http\Controllers\Admin;

use App\Model\Course;
use App\Model\Grade;
use Illuminate\Http\Request;
use App\Http\Controllers\CacheHandle;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomePage extends Controller
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
        $data['grade_list'] = Grade::withTrashed()->where('institute_id', $this->institute_id)
            ->select('id', 'name')
            ->get();
        return $this->json($data);
    }

}
