<?php

namespace App\Http\Controllers\Admin;

use App\Model\Classes;
use App\Model\Direction;
use App\Model\Grade;
use App\Model\Student;
use Faker\Provider\DateTime;
use Illuminate\Database\Eloquent\Model;
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
        $option['where'] = [
            ['institute_id','=',1], // 限制为自己学院
        ];
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
                $path = storage_path('app') . '/select-course/student_excel/'. $name;
                request()->file('excel')->move(storage_path('app') . '/select-course/student_excel/', $name);
                return $this->importStudent($path);
            }else{
                return $this->errorMsg('文件无效');
            }
        }else{
            return $this->errorMsg('请选择正确的文件类型');
        }
    }
    private function importStudent($excelPath){ // 处理用于上传的excel
        $phpexcel = new \PHPExcel_Reader_Excel2007();
        if(!$phpexcel->canRead($excelPath)){
            return ['msg'=> '不可读,仅支持EXCEL2007及以上的版本','status'=>false];
        }
        $sheet = $phpexcel->load($excelPath)->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $sheetHeader = $sheet->rangeToArray(
            'A1:' . 'C' . 1,
            NULL,TRUE,FALSE
        );
        $sheetbody = $sheet->rangeToArray(
            'A2:' . 'C' . $highestRow,
            NULL,TRUE,FALSE
        );
        if($sheetHeader[0] !== ['姓名','学号','班级代号']){
            return ['msg'=>'输入格式请规范,A1:姓名,A2:学号,A3:班级代号','status'=>false];
        }
        $insert = [];
        $classes_map = Classes::where('institute_id', $this->institute_id)
            ->lists('id','classes_code')->toArray();
        $major_map = Classes::where('institute_id', $this->institute_id)
            ->lists('major_id','classes_code')->toArray();
        $major_map[0] = 0;
        $classes_map[0] = 0;
        $create_at = date("Y-m-d H:i:s");

        $student = [ // 通用模板
            'created_at'=>$create_at,
            'updated_at'=>$create_at,
            'institute_id'=>$this->institute_id,
            'grade_id'=>Grade::where('institute_id', $this->institute_id)->value('id'),
        ];
        foreach( $sheetbody as $key=>$item) {
            if($item[0] && $item[1] && $item[2]){
                $student['name'] = $item[0];
                $student['job_num'] = $item[1];
                $student['classes_code'] = $item[2];
                if($major_map[$item[2]] && $classes_map[$item[2]]){
                    $student['major_id'] = $major_map[$item[2]];
                    $student['classes_id'] = $classes_map[$item[2]];
                    if(Student::where('institute_id',$this->institute_id)->where('job_num', $item[1])->count()){
                        return ['status'=>false,'msg'=>'请检查第'.($key+1) . '条数据,数据库中已存在该学号的学生,请校验'];
                    }else{
                        $insert[] = $student; // 压入待插入的数组
                    }

                }else{
                    return ['status'=>false,'msg'=>'请检查第'.($key+1) . '条数据,班级代号出错'];
                }
            }else{
                return ['status'=>false,'msg'=>'请检查第'. ($key+1) . '条数据,数据不能为空,且班级代号不能为0'];
            }
        }
        $result = Student::insert($insert);
        return ['status'=>$result, 'msg'=> ($result?'数据全部导入成功':'数据插入失败')];
    }
}
