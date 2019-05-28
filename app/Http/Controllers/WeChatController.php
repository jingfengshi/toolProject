<?php

namespace App\Http\Controllers;

use EasyWeChat;
use EasyWeChat\Kernel\Messages\Text;

class WeChatController extends Controller
{

    public function serve()
    {
        $app = app('wechat.official_account');
        $app->server->push(function($message)use($app){
            return $message['FromUserName'] ;
        });

        return $app->server->serve();
    }

}