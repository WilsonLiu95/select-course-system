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
                // ['direction_id',[1,2,3]],
                // ['major_id',[1,3]],
            ],
            'orderBy'=> [
                // 'id'=>'asc'
            ],
            'size'=> 20,
            'page'=> 1,

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

	public function makePage($handle, $option){

        $option = $this->option->merge($option);
//        $handle = $modelClass;
        if(count($option['filter'])){ // 过滤filter
            foreach($option['filter'] as $item){
                $handle->whereIn($item[0], $item[1]);
            }
        }
        if($option['search']['rule']){ // 搜索规则
            foreach ($option['search']['key'] as $item) {
                $handle->orWhere($item,'like','%'. $option['search']['rule'] .'%');
            }
        }
        if(count($option['orderBy'])){ // 排序规则
            foreach ($option['orderBy'] as $key=>$value) {
                $handle->orderBy($key, $value);
            }
        }
        $page = $handle->paginate($option['size'], ['*'],'page',$option['page'])->toArray();
        return $page;
    }
}

