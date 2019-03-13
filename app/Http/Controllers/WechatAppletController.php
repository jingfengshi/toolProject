<?php

namespace App\Http\Controllers;

use App\Models\WechatApplet;
use function EasyWeChat\Kernel\data_to_array;
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
        $arr = ['appid', 'name', 'status'];
        $data = WechatApplet::all($arr);
        return response()->json($data);
    }

    /**
     * 根据appid获取单挑数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItemByAppid()
    {
        $arr = ['appid', 'name', 'status'];
        $appid = Input::get('appid');
        $data = DB::table('wechat_applet')->where('appid', $appid)->select($arr)->first();
        return response()->json($data);
    }
}
