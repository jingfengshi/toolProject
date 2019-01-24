<?php

namespace App\Http\Controllers;

use EasyWeChat;
class WeChatController extends Controller
{

    public function serve()
    {
        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return "欢迎关注！";
        });

        return $app->server->serve();
    }

}