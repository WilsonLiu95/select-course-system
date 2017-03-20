<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
	use DispatchesJobs, ValidatesRequests;
	public function __construct()
	{

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
}
