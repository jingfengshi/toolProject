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
            $res = file_get_contents("http://111.67.193.162/api.php?sign=3358471198&url=".$not_dead);
            $jsondecode = json_decode($res, true);
            $code = $jsondecode['status'];
            // echo $code."///".$url."<br>";
            if($code == "0"){
                //如果已经拦截则对数据库进行修改
                LandDomain::where('url',$not_dead)->update(['is_dead'=>1]);
                $dead_urls[] = $not_dead;


            }
        }
        if(!empty($dead_urls)){
            $this->sendMail($dead_urls);

            $app = app('wechat.official_account');
            $urls= json_encode($dead_urls);
            $info_open_ids=[
                'oUBP90nVvieWm7Gw5mosi5l2ac-k',
                'oUBP90uyUhKbhZSK-EIAP-aQOXD4'
            ];
            foreach ($info_open_ids as $id){
                $app->template_message->send([
                    'touser' => $id,
                    'template_id' => 'jagxKqe1Yn90Ex5dXvdVWYg0R5vT8pZN-wv_b2Y-ylg',
                    'url' => 'https://www.baidu.com',
                    'data' => [
                        'first' => [
                            "value"=>'落地链接死亡:'.$urls
                        ],
                        'performance' =>[
                            "value"=>'请及时补充落地链接'
                        ],
                        'time'=>[
                            "value"=>date('Y-m-d H:i:s',time())
                        ],
                        'remark' => [
                            "value"=>'请及时补充落地链接'
                        ],
                    ],
                ]);
            }
        }


    }

    public function sendMail($dead_urls)
    {

        Mail::to([
            'jingfengshi@kooap.com',
            'konglingyan@kooap.com',
        ])->send(new InformateLandDomainDead($dead_urls));

    }
}