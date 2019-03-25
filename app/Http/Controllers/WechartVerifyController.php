<?php

namespace App\Http\Controllers;

use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Http\Request;

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
        $app = app('wechat.official_account');
        $app->server->push(function ($message) use ($app) {

            //上传图片
            $result = $app->material->uploadImage("/public/image/xcxqrcode.png");
            if ($result && $result['media-id']) {
                return new Image($result['media-id']);
            } else {
                return '';
            }
        });

        // 在 laravel 中：
        $response = $app->server->serve();
        return $response;
    }
}
