<?php

namespace App\Http\Controllers;

use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Http\Request;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Link;
class WechartVerifyController extends Controller
{
    //用于小程序后台第一步验证返回，验证成功后便可注释
    public function valid()
    {
//        $echoStr = $_GET["echostr"];
//        if ($this->checkSignature()) {
//            echo $echoStr;
//            exit;
//        }

        if (isset($_GET['echostr'])) {
            $echoStr = $_GET["echostr"];
            if ($this->checkSignature()) {
                echo $echoStr;
                exit;
            }
        } else {
            $this->responseMsg();
        }
    }

    //官方提供的验证demo
    public function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = 'flybird';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function responseMsg()
    {
        // {
        //    "media_id":MEDIA_ID,
        //    "url":URL
        // }
        $app = app('wechat.mini_program');
        $app->server->push(function ($message) use ($app) {
                $resp = new Link([
                     'title' => '欢迎点击查看全文',
                      'description' => '关注【浩然书城】公众号，查看更多免费小数',
                       'thumb_url'=>'https://toolproject.jinhuyingke03.com/image/qrcodethumb.jpg',
                      'url' => 'https://toolproject.jinhuyingke03.com/image/erweima.jpg']);
               return $app->customer_service->message($resp)->to($message['FromUserName'])->send();  
               return $resp;
        });

        // 在 laravel 中：
        $response = $app->server->serve();
        return $response;
    }
}
