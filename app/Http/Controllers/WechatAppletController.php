<?php

namespace App\Http\Controllers;

use App\Models\WechatApplet;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

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
        Log::info('getItemByAppid参数列表:', Input::get());
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
                $data['path'] = 'pages/index/index?from=89';
                $data['contenturl'] = 'https://wxf4da08c7e6b59e8b.yanyuzhuishu.com/read/7561/498734/2203/2';
                $data['app_id'] = 'wx1563d1fe8a291349';
            }

            $ip = $_SERVER["REMOTE_ADDR"];
            if ($ip) {
                for ($i = 0; $i < 3; $i++) {
                    $cityData = $this->getCity($ip);
                    if ($cityData) {
                        break;
                    } else {
                        time_sleep_until(time() + 1);
                    }
                }
            }
            if (isset($cityData) && $cityData) {
                $city = $cityData['city'];
            }

            if (isset($city) && $city) {
                $cityData = DB::table('wechat_mini_cities')->select()->where(['city' => $city, 'status'=>1])->get()->first();
                if ($cityData) {
                    $data['status'] = 0;
                }
            }
        } else {
            $data = array(
                'msg' => '签名验证失败'
            );
        }

        return response()->json($data);
    }

    /**
     * 获取 IP  地理位置
     * 淘宝IP接口
     * @Return: array
     * @param string $ip
     * @return array|bool|mixed
     */
    function getCity($ip)
    {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        try {
            $data = file_get_contents($url);
            $ipData = json_decode($data);
        } catch (ErrorException $exception) {
            Log::error($exception->getMessage());
        }
//        $ip=json_decode(file_get_contents($url));
        if (isset($ipData) && !is_null($ipData)) {
            if ((string)$ipData->code == '1') {
                return false;
            } else {
                $data = (array)$ipData->data;
                return $data;
            }
        } else {
            return false;
        }
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

    /**
     * 统计小说跳转情况
     * @param $appid
     * @param $status
     * @return string
     */
    public function countEnter($appid, $status)
    {
        $data = DB::table('wechat_applet')->where(['appid' => $appid])->select(['gh_id'])->first();
        if (!$data) {
            Log::error('WechatAppletController countEnter appid:' . $appid);
            return;
        }
        //获取小程序标识
        $ghid = $data->gh_id;
        $today = date('Ymd');
        $where = ['gh_id' => $ghid, 'ref_date' => $today];
        if ($status == 1) {   //用户发送文本消息
            if (DB::table('daily_wechat_mini_visit')->where($where)->first()) {
                DB::table('daily_wechat_mini_visit')->where($where)->increment('jump_success', 1, ['updated_at' => date('Y-m-d H:i:s')]);
            } else {
                DB::table('daily_wechat_mini_visit')->insert(['gh_id' => $ghid, 'appid' => $appid, 'ref_date' => $today, 'jump_success' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s')]);
            }
        } else if ($status == 2) {
            if (DB::table('daily_wechat_mini_visit')->where($where)->first()) {
                DB::table('daily_wechat_mini_visit')->where($where)->increment('jump_fail', 1, ['updated_at' => date('Y-m-d H:i:s')]);
            } else {
                DB::table('daily_wechat_mini_visit')->insert(['gh_id' => $ghid, 'appid' => $appid, 'ref_date' => $today, 'jump_fail' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s')]);
            }
        }
    }

    /**
     * 开放一个接口给新系统获取跳转数据
     * @param $day
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyWechatMiniVisit($day)
    {
        $visitData = DB::table('daily_wechat_mini_visit')->select(['appid', 'gh_id', 'ref_date', 'jump_success', 'jump_fail'])
            ->where(['ref_date' => $day])->get()->toArray();
        return response()->json($visitData);
    }

    /**
     * 开放一个接口给新系统日趋势获取数据
     * @param $day
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyVisitTrendData($day)
    {
        $visitData = DB::table('daily_visit_trend as DVT')->leftJoin('wechat_applet as WA', 'DVT.gh_id', '=', 'WA.gh_id')->select(['DVT.*', 'WA.appid'])
            ->where(['ref_date' => $day])->get();
        return response()->json($visitData);
    }
}
