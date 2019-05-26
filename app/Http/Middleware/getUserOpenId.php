<?php

namespace App\Http\Middleware;

use App\Services\getOpenID;
use Closure;

class getUserOpenId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        //获取openid

        //用户同意授权,获取code
        $appId = config('admin.wechat_ff.appId');
        $appSecret = config('admin.wechat_ff.secret');
        $openId =  new getOpenID($appId,$appSecret);
        $url = config('admin.wechat_ff.host');

        dd($request->url());
        $openId->get_authorize_url("snsapi_base", $url);


        return $next($request);
    }



}
