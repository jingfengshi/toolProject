<?php

namespace App\Http\Controllers;

use App\Mail\InformateWechat;
use App\Models\LandDomain;
use App\Models\OpenId;
use App\Services\getOpenID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WeChatRediectController extends Controller
{
    public function getCode()
    {
        //获取到code
        $code = request('code');
        //通过code  获取 token
        $appId = config('admin.wechat_ff.appId');
        $appSecret = config('admin.wechat_ff.secret');
        $openId =  new getOpenID($appId,$appSecret);
        $token =$openId->get_access_token($code);
        if(isset($token['errcode'])){
            Mail::to([
                'jingfengshi@kooap.com'
            ])->send(new InformateWechat($token));
        }else{
            $url =base64_decode(request('sbk'));
            $url=$url.'?openid='.$token['openid'];
            if(!OpenId::where('open_id',$token['openid'])->exists()){
                $area =getLocationByIp($ip=request()->ip());
                $device =getDevice();
                $net = getInternet()['net'];

                OpenId::create([
                    'ip'=>$ip,
                    'area'=>$area,
                    'device'=>$device.'/'.$net,
                    'open_id'=>$token['openid']
                ]);
            }else{
                $location = OpenId::where('open_id',$token['openid'])->first();
                if($location->block){
                    return redirect('http://'.LandDomain::where('is_dead',false)->first()->value('url').'/err');
                }
            }
            header("Location:{$url}");
        }


    }
}
