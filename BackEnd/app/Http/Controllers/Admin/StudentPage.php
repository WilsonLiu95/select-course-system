<?php

namespace App\Http\Controllers\Admin;

use App\Model\Classes;
use App\Model\Direction;
use App\Model\Grade;
use App\Model\Student;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CacheHandle;
class StudentPage extends Controller
{
    use CacheHandle;
    private $institute_id;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->institute_id = session()->get('institute_id');
    }

    public function postStudentInit(){
        $option = request()->all();
        $data['student_list'] = $this->makePage(Student::class, $option);

        // 制作映射关系
        $data['direction_map'] = Direction::where('institute_id', $this->institute_id)
            ->lists('name', 'id')->toArray();
        $data['direction_map'][0] = '尚未选定方向';
        $data['classes_map'] = Classes::where('institute_id', $this->institute_id)
            ->lists('name', 'id')->toArray();
        $data['classes_map'][0] = '尚未选定班级';


        return $this->json($data);
    }

    public function postStudentSubmit(){
        $this->validate(request(),[
            'id'=>'required|integer',
            'name'=>'required',
            'job_num'=>'required',
            'classes_id'=>'required|integer',
        ]);
        $new = request()->only('name','job_num','classes_id');
        if(request()->classes_id){
            $new['major_id'] = Classes::find(request()->classes_id)->major_id; // 更新majorId
        }else{
            $new['major_id'] = 0;
        }

        if(request()->id){
            // 修改classes_id但不修改classes_code 用于记录导入时是哪个班级
            Student::find(request()->id)->update($new);
        }else{
            // 新建的不写入classes_code 用以标识是手动创建
            $new['institute_id'] = $this->institute_id;
            $new['grade_id'] = Grade::where('institute_id', $this->institute_id)->value('id');
            \App\Model\Classes::create($new);
        }
        $this->cacheFlush($this->institute_id);
        return $this->json(['msg'=>'修改成功']);
    }
    public function postDelete(){
        $this->validate(request(),[
            'student_list'=>'required|array',
        ]);
        Student::where('institute_id',$this->institute_id)
            ->whereIn('id', request()->student_list)
            ->forceDelete();
        return $this->json(['msg'=>'删除成功']);
    }
    public function postFile(){ // 上传excel
        if(request()->hasFile('excel')){
            if(request()->file('excel')->isValid()){
                $name = 'ins' . $this->institute_id . '_' . request()->file('excel')->getClientOriginalName();

                request()->file('excel')->move(storage_path('app') . '/select-course/student_excel/', $name);
                return $this->json(['isSuccess'=>true]);
            }else{
                return $this->errorMsg('文件无效');
            }
        }else{
            return $this->errorMsg('请选择正确的文件类型');
        }

    }
}
