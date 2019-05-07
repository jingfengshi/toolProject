<?php

namespace App\Handlers;
use GuzzleHttp\Client;

class FangFengHandler
{

    public static function getShortUrl($url)
    {
        $http = new Client([
            'headers'=>[
                'Content-Type'=>'application/x-www-form-urlencoded'
            ]
        ]);

        // 初始化配置信息
        $api = 'http://www.dddpn.cn/index.php/index/api/direct';
        $username = config('admin.fangfeng.username');
        $key = config('admin.fangfeng.apiKey');


        // 发送 HTTP Post 请求
        $response = $http->post($api,[
            'form_params'=>[
                "username"     => $username,
                "apiKey"  => $key,
                "url"    => $url,
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        \Log::info(json_encode($result));
        if($result['code']!=200){
            return [];
        }
        return [
              'tourl'=>$result['tourl'],
        ];
    }

}