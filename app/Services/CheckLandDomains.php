<?php
/**
 * Created by PhpStorm.
 * User: rain
 * Date: 2019/5/26
 * Time: 12:00
 */

namespace App\Services;


use App\Mail\InformateLandDomainDead;
use App\Models\LandDomain;
use Illuminate\Support\Facades\Mail;

class CheckLandDomains
{
    public function handle()
    {
        $not_deads=LandDomain::where('is_dead',false)->pluck('url')->toArray();

        $dead_urls=[];
        foreach ($not_deads as $not_dead){
            $res = file_get_contents("http://111.67.193.162/api.php?sign=3358471198&url=1.".$not_dead);
            $jsondecode = json_decode($res, true);
            $code = $jsondecode['status'];
            // echo $code."///".$url."<br>";
            if($code == "0"){
                //如果已经拦截则对数据库进行修改
                LandDomain::where('url',$not_dead)->update(['is_dead',true]);
                $dead_urls[] = $not_dead;


            }
        }
        if(!empty($dead_urls)){
            $this->sendMail($dead_urls);
        }


    }

    public function sendMail($dead_urls)
    {

        Mail::to([
            'jingfengshi@kooap.com',
        ])->send(new InformateLandDomainDead($dead_urls));

    }
}