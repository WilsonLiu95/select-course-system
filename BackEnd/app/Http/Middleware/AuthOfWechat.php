<?php

namespace App\Http\Middleware;

use Closure;
use App\Model;

class AuthOfWechat
{

    public function __construct()
    {

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 判断是否微信授权过
        $res_data = array(
            "url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . env('WE_APPID') . "&redirect_uri=". urlencode(env('WE_BASE_PATH') . '/#/wechat') . "&response_type=code&scope=snsapi_base#wechat_redirect",
        );

        if (!session()->has("openid")){ // 任何页面的浏览都需要微信授权
            return response()->json($res_data,301);
        }

        $isIn = in_array($request->url(), [route('register'),route('is-login')]);

        if(!$isIn && !session()->has('id')){  // 微信授权后,如果没有登录,则只能浏览注册页面
            return response()->json([
                "option"=>['name'=>'register'],
                'msg'=>'请先注册'],301);
        }
        return $next($request);
    }
}
