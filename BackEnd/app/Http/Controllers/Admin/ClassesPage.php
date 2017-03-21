<?php

namespace App\Http\Controllers\Admin;

use App\Model\Grade;
use App\Model\Major;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CacheHandle;
class ClassesPage extends Controller
{
    use CacheHandle;
    private $institute_id;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->institute_id = session()->get('institute_id');
    }
    public function getClassesInit(){
        $data['classes_list'] = \App\Model\Classes::where('institute_id', $this->institute_id)
            ->get();
        $data['major_list'] = Major::where('institute_id', $this->institute_id)
            ->lists('name', 'id');
        $data['major_list'][0] = '不限专业';

        return $this->json($data);
    }
    public function postClassesSubmit(){
        $this->validate(request(),[
            'id'=>'required',
            'name'=>'required',
            'major_id'=>'required',
            'classes_code'=>'required',
        ]);
        $new = request()->only('name','major_id','classes_code','grade_id');
        $new['institute_id'] = $this->institute_id;
        $new['grade_id'] = Grade::where('institute_id', $this->institute_id)->value('id');
        if(request()->id){
            \App\Model\Classes::find(request()->id)->update($new);
        }else{
            \App\Model\Classes::create($new);
        }
        $this->cacheFlush($this->institute_id);
        return $this->json(['msg'=>'修改成功']);
    }
    public function getClassesDelete(){
        $this->validate(request(),[
            'id'=>'required',
        ]);
        \App\Model\Classes::find(request()->id)->forceDelete(); // 完全删除
        return $this->json(['msg'=>'删除成功']);
    }

}
