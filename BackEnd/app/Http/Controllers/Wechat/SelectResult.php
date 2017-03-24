<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CacheHandle;
class SelectResult extends Controller
{
    use BaseTrait,CacheHandle;
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
        // 有坑， 若学生已经选择了选修方向及课程，管理员更改其班级，导致专业变更，变更后的专业无法选择该方向，将造成此处出问题
        //  因此这里不做专业限制，以保证显示正常
        // $major_course = $this->cacheMajorCourse($this->account['institute_id'], $this->account['major_id']);
        $major_course = $this->cacheMajorCourse($this->account['institute_id'], 0);
        $direction_course = array_first($major_course,function($k, $v){
            return $v['id'] == $this->account['direction_id'];
        });
        $res['common_course'] = array_values($major_course[0]['course']->filter(function($item) use($has_select_common_course){
            return in_array($item['id'], $has_select_common_course);
        })->all());
        $res['direction_course'] = array_values($direction_course['course']->filter(function($item)
            use($has_select_direction_course){
                return in_array($item['id'], $has_select_direction_course);
        })->all());

        $res['system_config'] = $this->cacheSystemConfig($this->account['institute_id']);
        return $this->json($res);
    }
}
