<?php
/**
 * Created by PhpStorm.
 * User: rain
 * Date: 2019/5/26
 * Time: 10:27
 */

namespace App\Services;


use App\Mail\InformateSpreadDomainDead;
use App\Models\SpreadDomain;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckSpreadDomains
{

    public function handle()
    {
        $not_deads=SpreadDomain::where('is_dead',false)->pluck('url')->toArray();

        $dead_urls=['a.com'];
        foreach ($not_deads as $not_dead){
            $res = file_get_contents("http://111.67.193.162/api.php?sign=3358471198&url=1.".$not_dead);
            $jsondecode = json_decode($res, true);
            $code = $jsondecode['status'];
            // echo $code."///".$url."<br>";
            if($code == "0"){
                //如果已经拦截则对数据库进行修改
               SpreadDomain::where('url',$not_dead)->update(['is_dead',true]);
                $dead_urls[] = $not_dead;


            }
        }
        if(!empty($dead_urls)){
            $this->sendMail($dead_urls);
            $app = app('wechat.official_account');
        }


    }

    public function sendMail($dead_urls)
    {

            Mail::to([
                'jingfengshi@kooap.com',
            ])->send(new InformateSpreadDomainDead($dead_urls));

    }

}