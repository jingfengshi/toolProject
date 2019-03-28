<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 10:20
 */

namespace App\Services;


use Illuminate\Support\Facades\Log;

class AliyunUtils
{
    /**
     * 获取post请求数据
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function requestPost($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . strlen($params)));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    /**
     * get请求
     * @param string $url
     * @return mixed
     */
    public static function requestGet(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $res = curl_exec($ch);
        return json_decode($res, true);
    }

    /**
     * @param $url
     * @param array $data
     * @param string $version "2018-01-29" 注册域名 "2015-01-09" 解析域名
     * @return string
     */
    public static function getSignature($url, $data = [], $version = '2018-01-29')
    {
        $key = env('ACCESS_KEY_ID', '');  //这里是阿里云的accesskeyid 和accesskeysecret
        $secret = env('ACCESS_KEY_SECRET', '');
        //这是请求api 的公共请求参数，
        $publicParams = array(
            "Format" => "JSON",
            "Version" => $version,
            "AccessKeyId" => $key,
            "Timestamp" => date('Y-m-d\TH:i:s\Z', time() - date('Z')),
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureVersion" => "1.0",
            "SignatureNonce" => substr(md5(rand(1, 99999999)), rand(1, 9), 14),
        );
        $params = array_merge($publicParams, $data);
        $params['Signature'] = self::signature($params, $secret);
        $uri = http_build_query($params);
        $url = $url . '/?' . $uri;
        return $url;
    }

    public static function signature($params, $accessSecret, $method = "GET")
    {
        ksort($params);
        $stringToSign = strtoupper($method) . '&' . self::percentEncode('/') . '&';
        $tmp = "";
        foreach ($params as $key => $val) {
            $tmp .= '&' . self::percentEncode($key) . '=' . self::percentEncode($val);
        }
        $tmp = trim($tmp, '&');
        $stringToSign = $stringToSign . self::percentEncode($tmp);
        $key = $accessSecret . '&';
        $hmac = hash_hmac("sha1", $stringToSign, $key, true);
        return base64_encode($hmac);
    }

    public static function percentEncode($value = null)
    {
        $en = urlencode($value);
        $en = str_replace("+", "%20", $en);
        $en = str_replace("*", "%2A", $en);
        $en = str_replace("%7E", "~", $en);
        return $en;
    }


    /**
     * 购买
     * @return mixed
     */
    public function test()
    {
        $url = 'http://domain.aliyuncs.com';
        $data = array('Action' => 'QueryRegistrantProfiles');
        $result = AliyunUtils::requestGet(AliyunUtils::getSignature($url, $data));
        //获取信息模板id
        $registrantProfileId = $result['RegistrantProfiles']['RegistrantProfile'][0]['RegistrantProfileId'];

        //购买
        $data = array('Action' => 'SaveSingleTaskForCreatingOrderActivate', 'DomainName' => 'alcg7.cn', 'RegistrantProfileId' => $registrantProfileId);
        $result = AliyunUtils::requestGet(AliyunUtils::getSignature($url, $data));
        return $result;
    }

    /**
     * 获取信息模板id
     * @return mixed
     */
    public static function registrantProfileId()
    {
        $url = 'http://domain.aliyuncs.com';
        $data = array('Action' => 'QueryRegistrantProfiles');
        $result = AliyunUtils::requestGet(AliyunUtils::getSignature($url, $data));
        //获取信息模板id
        $registrantProfileId = $result['RegistrantProfiles']['RegistrantProfile'][0]['RegistrantProfileId'];
        return $registrantProfileId;
    }

    /**
     * 检查域名是否可以注册
     * @param $domainName
     * @param string $url
     * @return mixed
     */
    public static function checkDomain($domainName, $url = 'http://domain.aliyuncs.com')
    {
        $data = array('Action' => 'CheckDomain', 'DomainName' => $domainName);
        $result = AliyunUtils::requestGet(AliyunUtils::getSignature($url, $data));
        return $result;
    }

    /**
     * 提交批量域名注册任务
     * @param array $domains
     * @param string $url
     * @return mixed
     */
    public static function saveBatchTaskForCreatingOrderActivate($domains = [], $url = 'http://domain.aliyuncs.com')
    {
        $data = array('Action' => 'SaveBatchTaskForCreatingOrderActivate');
        $registrantProfileId = self::registrantProfileId();
        foreach ($domains as $key => $value) {
            $index = $key + 1;
            $data['OrderActivateParam' . '.' . $index . '.' . 'DomainName'] = $value;
            $data['OrderActivateParam' . '.' . $index . '.' . 'RegistrantProfileId'] = $registrantProfileId;
        }
        $result = AliyunUtils::requestGet(AliyunUtils::getSignature($url, $data));
        return $result;
    }


    /**
     * 查询已经执行完成的任务详情列表
     * @param string $url
     * @param $taskNo
     * @return mixed
     */
    public static function pollTaskResult($taskNo, $url = 'http://domain.aliyuncs.com')
    {
        $data = array('Action' => 'PollTaskResult', 'PageNum' => 1, 'PageSize' => 100, 'TaskNo' => $taskNo);
        $result = AliyunUtils::requestGet(AliyunUtils::getSignature($url, $data));
        return $result;
    }

    /**
     * 添加解析记录
     * @param array $domains
     * @param string $url
     * @return mixed
     */
    public static function addDomainRecord($domains = [], $url = 'http://alidns.aliyuncs.com')
    {
        if ($domains) {
            foreach ($domains as $key => $value) {
                $data = array('Action' => 'AddDomainRecord', 'DomainName' => 'alcg9.cn', 'RR' => self::str_rand(4), 'Type' => 'A', 'Value' => '47.107.59.162');
                $result = AliyunUtils::requestGet(AliyunUtils::getSignature($url, $data, '2015-01-09'));
            }
        }
    }

    /**
     * 批量解析记录
     * @param array $domains
     * @param string $url
     * @param string $ip
     * @return mixed
     */
    public static function operateBatchDomain($domains = [], $ip = '47.107.59.162', $url = 'http://alidns.aliyuncs.com')
    {
        if ($domains) {
            $data = array('Action' => 'OperateBatchDomain', 'Type' => 'RR_ADD');
            foreach ($domains as $key => $value) {
                $index = $key + 1;
                $data['DomainRecordInfo' . '.' . $index . '.' . 'Domain'] = $value['DomainName'];
                $data['DomainRecordInfo' . '.' . $index . '.' . 'Type'] = 'A';
                $rr = self::str_rand(4);
                $data['DomainRecordInfo' . '.' . $index . '.' . 'Rr'] = $rr;
                $data['DomainRecordInfo' . '.' . $index . '.' . 'Value'] = $ip;  //动态获取

                $domains[$key]['rr'] = $rr;
                $domains[$key]['ip'] = $ip;
            }
            $result = AliyunUtils::requestGet(AliyunUtils::getSignature($url, $data, '2015-01-09'));

            return array('domain' => $domains, 'dns_taskid' => isset($result['TaskId']) ? $result['TaskId'] : '0000');
        }
    }

    /**
     * 批量解析记录
     * @param $taskid
     * @param string $url
     * @return mixed
     */
    public static function describeBatchResultCount($taskid, $url = 'http://alidns.aliyuncs.com')
    {
        $data = array('Action' => 'DescribeBatchResultCount', 'TaskId' => $taskid);
        $result = AliyunUtils::requestGet(AliyunUtils::getSignature($url, $data, '2015-01-09'));
        return $result;
    }

    /**
     * 生成随机字符串
     * @param int $length 生成随机字符串的长度
     * @param string $char 组成随机字符串的字符串
     * @return string $string 生成的随机字符串
     */
    public static function str_rand($length = 32, $char = '0123456789abcdefghijklmnopqrstuvwxyz')
    {
        if (!is_int($length) || $length < 0) {
            return false;
        }
        $string = '';
        for ($i = $length; $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }
        return $string;
    }
}
