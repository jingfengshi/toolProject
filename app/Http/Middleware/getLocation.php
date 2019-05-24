<?php

namespace App\Http\Middleware;

use App\Models\Location;
use Closure;

class getLocation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $ip=$request->ip();
        if(!Location::where('ip',$ip)->exists()){
            $area =$this->getLocationByIp($ip);
            $device =$this->getDevice();
            $net = $this->getInternet()['net'];

            Location::create([
                'ip'=>$ip,
                'location'=>$area,
                'device'=>$device.'/'.$net
            ]);
        }else{
            $location = Location::where('ip',$ip)->first();
            if($location->block){
                return redirect('/err');
            }
        }

        return $next($request);
    }


    public function getLocationByIp($ip)
    {
        error_reporting(0);
        $area = "";
        $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ip=json_decode(file_get_contents($url));
        if((string)$ip->code==0){
            $json = (array)$ip->data;
            if (isset($json['city']) && $json['city']) {
                return $json['city'];
            }
            if (isset($json['region']) && $json['region']) {
                return $json['region'];
            }
            if (isset($json['country']) && $json['country']) {
                return $json['country'];
            }
        }else{
            return '';
        }
    }


    public function getDevice()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (stripos($user_agent, "iPhone")!==false) {
            $brand = 'iPhone';
        } else if (stripos($user_agent, "SAMSUNG")!==false || stripos($user_agent, "Galaxy")!==false || strpos($user_agent, "GT-")!==false || strpos($user_agent, "SCH-")!==false || strpos($user_agent, "SM-")!==false) {
            $brand = '三星';
        } else if (stripos($user_agent, "Huawei")!==false || stripos($user_agent, "Honor")!==false || stripos($user_agent, "H60-")!==false || stripos($user_agent, "H30-")!==false) {
            $brand = '华为';
        } else if (stripos($user_agent, "Lenovo")!==false) {
            $brand = '联想';
        } else if (strpos($user_agent, "MI-ONE")!==false || strpos($user_agent, "MI 1S")!==false || strpos($user_agent, "MI 2")!==false || strpos($user_agent, "MI 3")!==false || strpos($user_agent, "MI 4")!==false || strpos($user_agent, "MI-4")!==false) {
            $brand = '小米';
        } else if (strpos($user_agent, "HM NOTE")!==false || strpos($user_agent, "HM201")!==false) {
            $brand = '红米';
        } else if (stripos($user_agent, "Coolpad")!==false || strpos($user_agent, "8190Q")!==false || strpos($user_agent, "5910")!==false) {
            $brand = '酷派';
        } else if (stripos($user_agent, "ZTE")!==false || stripos($user_agent, "X9180")!==false || stripos($user_agent, "N9180")!==false || stripos($user_agent, "U9180")!==false) {
            $brand = '中兴';
        } else if (stripos($user_agent, "OPPO")!==false || strpos($user_agent, "X9007")!==false || strpos($user_agent, "X907")!==false || strpos($user_agent, "X909")!==false || strpos($user_agent, "R831S")!==false || strpos($user_agent, "R827T")!==false || strpos($user_agent, "R821T")!==false || strpos($user_agent, "R811")!==false || strpos($user_agent, "R2017")!==false) {
            $brand = 'OPPO';
        } else if (strpos($user_agent, "HTC")!==false || stripos($user_agent, "Desire")!==false) {
            $brand = 'HTC';
        } else if (stripos($user_agent, "vivo")!==false) {
            $brand = 'vivo';
        } else if (stripos($user_agent, "K-Touch")!==false) {
            $brand = '天语';
        } else if (stripos($user_agent, "Nubia")!==false || stripos($user_agent, "NX50")!==false || stripos($user_agent, "NX40")!==false) {
            $brand = '努比亚';
        } else if (strpos($user_agent, "M045")!==false || strpos($user_agent, "M032")!==false || strpos($user_agent, "M355")!==false) {
            $brand = '魅族';
        } else if (stripos($user_agent, "DOOV")!==false) {
            $brand = '朵唯';
        } else if (stripos($user_agent, "GFIVE")!==false) {
            $brand = '基伍';
        } else if (stripos($user_agent, "Gionee")!==false || strpos($user_agent, "GN")!==false) {
            $brand = '金立';
        } else if (stripos($user_agent, "HS-U")!==false || stripos($user_agent, "HS-E")!==false) {
            $brand = '海信';
        } else if (stripos($user_agent, "Nokia")!==false) {
            $brand = '诺基亚';
        } else {
            $brand = '其他';
        }
        return  $brand;
    }


    public function getInternet($ua='')
    {
        if (!$ua) {
            $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
        }
        $pre1 = '/^Mozilla\/[^(]+\(([^\)]+).*NetType\/(\w*)/u';
        $pre2 = '/Mozilla\/[^(]+\(.*(Android|iPhone|iPad).*NetType\/(\w*)/u';
        preg_match($pre2, $ua, $res);
        $result["sys"] = isset($res[1]) ? $res[1] : 0;
        $result["net"] = isset($res[2]) ? $res[2] : 0;
        return $result;
    }
}
