<?php

namespace App\Http\Controllers\Admin;

use App\Model\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\CacheHandle;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class Home extends Controller
{
    use CacheHandle;
    private $institute_id;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->institute_id = session()->get('institute_id');
    }
    public function getCourse(){
        $course = $this->cacheMajorCourse($this->institute_id, 0);
        return $this->json($course);
    }

}
