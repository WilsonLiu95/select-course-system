<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use App\Model;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Controllers\CacheHandle;
class Wechat extends Controller
{
    use BaseTrait,CacheHandle;
    public function getIndex(Request $request)
    {
        $code = $request->query("code");
        // 请求中需要带上code,否则无法进行微信认证
        if (!isset($code)){
            return $this->errorMsg("微信授权code不存在");
        }
        
        $getUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . env("WE_APPID") ."&secret=" .
            env('WE_SECRET') . "&code=$code&grant_type=authorization_code";
        
        $client = new \GuzzleHttp\Client(['base_uri'=>'https://api.weixin.qq.com','verify' => false]); // 省去SSL的证书，防止某些机子没有SSL证书造成请求失败
        $res = $client->request('GET', $getUrl);
        $body = \GuzzleHttp\json_decode($res->getBody());

        if (isset($body->errcode)){
            // 说明验证码无效
            return $this->errorMsg("系统错误，请重新登录");
        }
        // 微信授权成功

        session()->put("openid",$body->openid);

        $student = Model\Student::where("openid",$body->openid);
        if ($student->exists()) {
            // 用户已绑定过账号
            session()->put("id",$student->value('id'));
        }
        return response()->json(['url'=> env("BASE_PATH"),],301);

    }
}
