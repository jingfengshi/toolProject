<?php

namespace App\Http\Controllers;

use App\Services\WeChat;

class WechartVerifyController extends Controller
{
    //用于小程序后台第一步验证返回，验证成功后便可注释
    public function valid()
    {
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
//        $app = app('wechat.mini_program');
//        $message = $app->server->getMessage();
//        $ghid = $message['ToUserName'];
//        $postStr = file_get_contents('php://input');
//        if (!empty($postStr) && is_string($postStr)) {
//            $postArr = json_decode($postStr, true);
//        } else {
//            return "empty";
//        }
//        //获取小程序标识
//        $ghid = $postArr['ToUserName'];
//        $arr = array('appid', 'appsecret', 'aeskey', 'token', 'name');
//        $data = DB::table('wechat_applet')->where('gh_id', $ghid)->select($arr)->first();
//        $data = get_object_vars($data);
//        Log::info('WechartVerifyController' . $data['name'], $data);
//
//
//        $config = [
//            'app_id' => $data['appid'],
//            'secret' => $data['appsecret'],
//            'token' => $data['token'],
//            'aes_key' => $data['aeskey'],
//        ];
//        $app = Factory::officialAccount($config);
//        $app->server->push(function ($message) use ($app) {
//            $where = array('gh_id' => $message['ToUserName'], 'status' => 1);
//            $data = DB::table('message_template')->where($where)->select()->first();
//            if (!$data) {
//                $data = DB::table('message_template')->select()->first();
//                $domain = 'https://toolproject.jinhuyingke03.com/upload/';
//                if (!$data) {
//                    $resp = new Link([
//                        'title' => '欢迎点击查看全文',
//                        'description' => '关注【浩然书城】公众号，查看更多免费小数',
//                        'thumb_url' => 'https://toolproject.jinhuyingke03.com/image/qrcodethumb.jpg',
//                        'url' => 'https://toolproject.jinhuyingke03.com/image/qrcode.jpg'
//                    ]);
//                } else {
////                    Log::info($data);
//                    $data = get_object_vars($data);
//                    $resp = new Link([
//                        'title' => $data['title'],
//                        'description' => $data['description'],
//                        'thumb_url' => $domain . $data['thumb_url'],
//                        'url' => $domain . $data['url']
//                    ]);
//                }
//            } else {
//                $data = get_object_vars($data);
//                switch ($data['type']) {
//                    case 'text':
//                        $resp = new Text($data['content']);
//                        break;
//                    case 'image':
//                        $resp = new Image($data['media_id']);
//                        break;
//                    case 'link':
//                        $resp = new Link([
//                            'title' => $data['title'],
//                            'description' => $data['description'],
//                            'thumb_url' => $data['thumb_url'],
//                            'url' => $data['url']]);
//                        break;
//                    case 'miniprogrampage':
//                        $resp = new MiniProgramPage([
//                            'title' => $data['title'],
//                            'pagepath' => $data['pagepath'],
//                            'thumb_media_id' => $data['media_id']
//                        ]);
//                        break;
//                }
//            }
//            return $app->customer_service->message($resp)->to($message['FromUserName'])->send();
//        });
//
//        // 在 laravel 中：
//        try {
//            $app->server->serve();
//        } catch (InvalidArgumentException $invalidArgumentException) {
//            Log::error($invalidArgumentException->getMessage());
//        }
////        return $response;
        $wechat = new WeChat();
        $wechat->responseMSG();
    }
}
