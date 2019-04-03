<?php

namespace App\Http\Controllers;

use App\Models\WechatApplet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class WechatAppletController extends Controller
{
    /**
     * 获取所有请求
     * @return \Illuminate\Http\JsonResponse
     */
    public function showLists()
    {
        $arr = ['appid', 'name', 'status', 'alias', 'domain'];
        $data = WechatApplet::all($arr);
        return response()->json($data);
    }

    /**
     * 根据appid获取单挑数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItemByAppid()
    {
        $appSecret = env('ACCOUNT_SECRET', '');
        $arr = ['appid', 'name', 'status', 'alias', 'domain'];
        $appid = Input::get('appid');
        $params = array(
            "appid" => $appid,
            "timestamp" => Input::get('timestamp'),
        );
        if ($this->signature($params, $appSecret) == Input::get('sign')) {
            $data = DB::table('wechat_applet')->where('appid', $appid)->select($arr)->first();

            if (!is_array($data)) {
                $data = get_object_vars($data);
            }

            if ($data['status']) {
//                $data['url'] = 'https://m.zhenzumei.com/detail/4779/1135310/9578/1.html';
//                $data['imgUrl'] = 'https://toolproject.jinhuyingke03.com/image/xiaoshuo02.jpg';
                $wechatContent = DB::table('wechat_content')->select()->first();
                $wechatContent = get_object_vars($wechatContent);
                $data['url'] = $wechatContent['url'];
                $data['imgUrl'] = $wechatContent['domain'] . '/upload/' . $wechatContent['imgUrl'];
                $data['alias'] = $wechatContent['alias'];
            }
        } else {
            $data = array(
                'msg' => '签名验证失败'
            );
        }

        return response()->json($data);
    }

    /**
     * 签名
     * @param $params
     * @param $accessSecret
     * @return string
     */
    public static function signature($params, $accessSecret)
    {
        ksort($params);
        $stringToSign = '';

        foreach ($params as $key => $val) {
            $stringToSign .= $val;
        }
        $stringToSign .= $accessSecret;
        return md5($stringToSign, false);
    }
}
