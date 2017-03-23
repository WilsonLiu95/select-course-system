<?php

namespace App\Http\Controllers;

use App\Model;
use Faker\Provider\zh_TW\DateTime;
use Flexihash\Flexihash;
use GuzzleHttp\Cookie\SessionCookieJar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Psy\Util\Json;
use Validator;
use App\Http\Requests;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Wechat\BaseTrait;
use Illuminate\Support\Facades\Storage;
use \PHPExcel_IOFactory;
use \PHPExcel;
class Test extends Controller
{
    use BaseTrait;
    public function getIndex()
    {
//        $path = storage_path('app') . '/select-course/student_excel/ins1_test.xlsx';
//
//        $reader = new \PHPExcel_Reader_Excel2007();
//        return $this->json($reader->canRead($path));
//        $currentSheet = $reader->load($path)->getSheet(0);
//
        $a = array_has([2=>1], 2);

//array_has()
        return $this->json($a);
    }


    public function getDelete(){
        Model\Student::whereIn('grade_id',[1,2])->delete();
        Model\Classes::whereIn('grade_id',[1,2])->delete();
        Model\SelectCourse::whereIn('grade_id',[1,2])->delete();
    }
    public function getMakeExcel(){

        $a = new PHPExcel();
        $fileName = "test_excel";
        $headArr = array("姓名","学号","班级代号");
        $data = Model\Student::where('institute_id',1)
            ->select('name', 'job_num', 'classes_code')
            ->get()->toArray();
//        $data = array(array("蔡依林","2038010501","90"),array("潘玮柏","2038010502","91"),array("柳下惠","2038010503","80"));
        $this->getExcel($fileName,$headArr,$data);


    }
   private function getExcel($fileName,$headArr,$data){
        if(empty($data) || !is_array($data)){
            die("data must be a array");
        }
        if(empty($fileName)){
            exit;
        }
        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xlsx";

        //创建新的PHPExcel对象
        $objPHPExcel = new PHPExcel();
        $objProps = $objPHPExcel->getProperties();

        //设置表头,从第二列开始
        $key = ord("A");
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        //遍历二维数组的数据
        foreach($data as $key => $rows){
            $span = ord("A");
            // 列写入
            foreach($rows as $keyName=>$value){
                $j = chr($span);
                //按照B2,C2,D2的顺序逐个写入单元格数据
                $objActSheet->setCellValue($j.$column, $value);
                //移动到当前行右边的单元格
                $span++;
            }
            //移动到excel的下一行
            $column++;
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //脚本方式运行，保存在当前目录
        //$objWriter->save($fileName);

        // 输出文档到页面
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="test.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save("php://output");
//        exit;

    }
}
