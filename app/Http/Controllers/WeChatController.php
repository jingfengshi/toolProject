<?php

namespace App\Http\Controllers;

use EasyWeChat;
use EasyWeChat\Kernel\Messages\Text;

class WeChatController extends Controller
{

    public function serve()
    {
        $app = app('wechat.official_account');
        $app->server->push(function($message){
            $text = new Text('dfsafsdfsdf');
            return $text;
        });

        return $app->server->serve();
    }

}