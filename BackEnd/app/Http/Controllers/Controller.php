<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;

abstract class Controller extends BaseController
{
	use DispatchesJobs, ValidatesRequests;
    private $option;
	public function __construct()
	{
        $this->option = collect([
            'search'=>[
                // 'key'=> ['job_num','name'], // orwhere 查询
                // 'rule'=> '759',
            ],
            'filter'=>[
//                 'direction_id'=>[1,2,3],
                // 'major_id'=>[1,3],
            ],
            'orderBy'=> [
                'key'=>'id',
                'order'=>'asc'
            ],
            'size'=> 20,
            'page'=> 1,
            'where'=>[
                ['institute_id', '=', 1], // where筛选
            ],
            'col'=>'*', // 搜索哪些字段值,为数组
        ]);
	}
	public function json($data=array(),$http_code=200){
			return response()->json($data,$http_code);
	}

	public function redirect($option=array(), $msg=""){
		// 如果要填路径则$option为路径
				return response()->json([
				            "option"=>$option,
                            'msg'=>$msg],301);
	}
	public function errorMsg($msg="")
	{
		$res = [
			'msg' => $msg
		];
		return response()->json($res, 400);
    }

	public function makePage($modelString, $option){ // 制表
        $option = $this->option->merge($option);
        $model = app($modelString); // 获取模型
         // 经验证必须加入一道操作,否则后面的操作都不会生效,所以将字段select加在此处
        $handle = $model->select($option['col']);
        foreach($option['where'] as $item){
         $handle->where($item[0],$item[1],$item[2]);
        }
        $handle->where(function($query)use($option){ // search关键词模糊匹配
            if($option['search'] && $option['search']['rule']){ // 搜索规则
                foreach ($option['search']['key'] as $key=>$item) {
                    if($key==0){
                        $query->where($item,'like','%'. $option['search']['rule'] .'%');
                    }else{
                        $query->orWhere($item,'like','%'. $option['search']['rule'] .'%');
                    }
                };

            }
        });
        if(count($option['orderBy'] )&& $option['orderBy']['key'] && $option['orderBy']['order']) { // 排序
            $handle->orderBy($option['orderBy']['key'], $option['orderBy']['order']);

        }
        // 过滤filter

        if(count($option['filter'])){
            foreach($option['filter'] as $key=>$value){
                $handle->whereIn($key, $value);
            }
        };

        $data = $handle->paginate($option['size'], ['*'],'page',$option['page'])->toArray();
        return $data;
    }
}

