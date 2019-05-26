<?php

namespace App\Http\Controllers;

use App\Services\getOpenID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WeChatRediectController extends Controller
{
    public function getCode()
    {
        //获取到code
        $code = request('code');
        //Log::error('11111');
       // Log::error($code);
        //通过code  获取 token
        $appId = config('admin.wechat_ff.appId');
        $appSecret = config('admin.wechat_ff.secret');

        $openId =  new getOpenID($appId,$appSecret);

        $token =$openId->get_access_token($code);
        Log::error(json_encode($token));
    }
}
