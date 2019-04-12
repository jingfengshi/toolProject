<?php

/**
 * 微信公众平台操作类
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeChat
{
    private $_appid;
    private $_appsecret;

    public function responseMSG()
    {
        $postStr = file_get_contents('php://input');
        if (!empty($postStr) && is_string($postStr)) {
            $postArr = json_decode($postStr, true);
            Log::info($postStr);
        } else {
            return "empty";
        }

        //获取小程序标识
        $ghid = $postArr['ToUserName'];
        $today = date('Ymd');
        $where = ['gh_id' => $ghid, 'ref_date' => $today];
        if (!empty($postArr['MsgType']) && $postArr['MsgType'] == 'event') {   //用户发送文本消息
            if (DB::table('daily_wechat_mini_visit')->where($where)->first()) {
                DB::table('daily_wechat_mini_visit')->where($where)->increment('enter_times', 1, ['updated_at'=>date('Y-m-d H:i:s')]);
            } else {
                DB::table('daily_wechat_mini_visit')->insert(['gh_id' => $ghid, 'ref_date' => $today, 'enter_times' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s')]);
            }
        } else {
            if (DB::table('daily_wechat_mini_visit')->where($where)->first()) {
                DB::table('daily_wechat_mini_visit')->where($where)->increment('reply_times', 1, ['updated_at'=>date('Y-m-d H:i:s')]);
            } else {
                DB::table('daily_wechat_mini_visit')->insert(['gh_id' => $ghid, 'ref_date' => $today, 'reply_times' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s')]);
            }
        }


        $arr = array('appid', 'appsecret', 'aeskey', 'token', 'name');
        $configdata = DB::table('wechat_applet')->where('gh_id', $ghid)->select($arr)->first();
        $configdata = get_object_vars($configdata);
        $this->_appid = $configdata['appid'];
        $this->_appsecret = $configdata['appsecret'];
        Log::info('WechartVerifyController' . $configdata['name'], $configdata);

        $where = array('gh_id' => $ghid, 'status' => 1);
        $data = DB::table('message_template')->where($where)->select()->first();
        if (!$data) {
            $data = DB::table('message_template')->select()->first();
            $domain = 'https://toolproject.jinhuyingke03.com/upload/';
            $fromUsername = $postArr['FromUserName'];
            if (!$data) {
                Log::info('默认消息');
                $data = array(
                    "touser" => $fromUsername,
                    "msgtype" => "link",
                    "link" => array('title' => '欢迎点击查看全文',
                        'description' => '关注【浩然书城】公众号，查看更多免费小数',
                        'thumb_url' => 'https://toolproject.jinhuyingke03.com/image/qrcodethumb.jpg',
                        'url' => 'https://toolproject.jinhuyingke03.com/image/qrcode.jpg')
                );
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);
                $this->requestAPI($json, $ghid);
            } else {
                Log::info('模板消息');
                $data = get_object_vars($data);
                $data = array(
                    "touser" => $fromUsername,
                    "msgtype" => "link",
                    "link" => array(
                        'title' => $data['title'],
                        'description' => $data['description'],
                        'thumb_url' => $domain . $data['thumb_url'],
                        'url' => $domain . $data['url']
                    )
                );
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);
                $this->requestAPI($json, $ghid);
            }
        }
    }

    public function responseMSGtest()
    {
        //获取小程序标识
        $ghid = 'gh_1dd2529af212';
        $this->_appid = 'wxe91dda840df59531';
        $this->_appsecret = 'a262f2acd2f51a8ea12bfb6bc0cf9673';

        $fromUsername = 'ooGRH43kRlnfxJ6j5uHnIsdTzh_o';   //此处为文字回复，不同的回复方式可参考文章顶部第三个链接“回复客户消息”里查看
        $data = array(
            "touser" => $fromUsername,
            "msgtype" => "link",
            "link" => array('title' => '欢迎点击查看全文',
                'description' => '关注【浩然书城】公众号，查看更多免费小数',
                'thumb_url' => 'https://toolproject.jinhuyingke03.com/image/qrcodethumb.jpg',
                'url' => 'https://toolproject.jinhuyingke03.com/image/qrcode.jpg')
        );

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->requestAPI($json, $ghid);
    }

    public function requestAPI($json, $ghid)
    {
        $access_token = $this->get_accessToken($ghid);

        //POST发送https请求客服接口api
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $access_token;
        //以'json'格式发送post的https请求
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($json)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);//捕抓异常
        }
        curl_close($curl);
        if ($output == 0) {
            echo 'success';
            exit;
        }
    }

    /**
     * @param $ghid
     * @param string $token_file_path
     * @return bool|false|string
     */
    private function get_accessToken($ghid, $token_file_path = './access_token')
    {
        // 考虑过期问题，将获取的access_token存储到某个文件中
        $life_time = 7100;
        if (!file_exists($token_file_path)) {
            mkdir($token_file_path, 0777, true);
        }
        $token_file = $token_file_path . DIRECTORY_SEPARATOR . $ghid;
        if (file_exists($token_file) && time() - filemtime($token_file) < $life_time) {
            // 存在有效的access_token
            return file_get_contents($token_file);
        }
        // 目标URL：
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->_appid}&secret={$this->_appsecret}";
        //向该URL，发送GET请求
        $result = $this->_requestGet($url);
        Log::info($result);
        if (!$result) {
            return false;
        }
        // 存在返回响应结果
        $result_obj = json_decode($result, true);
        // 写入
        file_put_contents($token_file, $result_obj['access_token']);
        return $result_obj['access_token'];
    }

    private function _requestPost($url, $data, $ssl = true)
    {
        // curl完成
        $curl = curl_init();

        //设置curl选项
        curl_setopt($curl, CURLOPT_URL, $url);//URL
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);//referer头，请求来源
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);//设置超时时间
        //SSL相关
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//禁用后cURL将终止从服务端进行验证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//检查服务器SSL证书中是否存在一个公用名(common name)。
        }
        // 处理post相关选项
        curl_setopt($curl, CURLOPT_POST, true);// 是否为POST请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);// 处理请求数据
        // 处理响应结果
        curl_setopt($curl, CURLOPT_HEADER, false);//是否处理响应头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//curl_exec()是否返回响应结果

        // 发出请求
        $response = curl_exec($curl);
        if (false === $response) {
            echo '<br>', curl_error($curl), '<br>';
            return false;
        }
        curl_close($curl);
        return $response;
    }

    /**
     * 发送GET请求的方法
     * @param string $url URL
     * @param bool $ssl 是否为https协议
     * @return string 响应主体Content
     */
    private function _requestGet($url, $ssl = true)
    {
        // curl完成
        $curl = curl_init();

        //设置curl选项
        curl_setopt($curl, CURLOPT_URL, $url);//URL
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);//referer头，请求来源
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);//设置超时时间

        //SSL相关
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//禁用后cURL将终止从服务端进行验证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//检查服务器SSL证书中是否存在一个公用名(common name)。
        }
        curl_setopt($curl, CURLOPT_HEADER, false);//是否处理响应头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//curl_exec()是否返回响应结果

        // 发出请求
        $response = curl_exec($curl);
        if (false === $response) {
            echo '<br>', curl_error($curl), '<br>';
            return false;
        }
        curl_close($curl);
        return $response;
    }

}
