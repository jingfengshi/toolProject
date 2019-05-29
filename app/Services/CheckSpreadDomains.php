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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckSpreadDomains
{

    public function handle()
    {
        $not_deads=SpreadDomain::where('is_dead',false)->pluck('url')->toArray();


        $dead_urls=[];
        foreach ($not_deads as $not_dead){
            $res = file_get_contents("http://111.67.193.162/api.php?sign=3358471198&url=1.".$not_dead);
            $jsondecode = json_decode($res, true);
            $code = $jsondecode['status'];
            // echo $code."///".$url."<br>";
            if($code == "0"){
                //如果已经拦截则对数据库进行修改
               SpreadDomain::where('url',$not_dead)->update(['is_dead'=>1]);
                $dead_urls[] = $not_dead;


            }
        }
        if(!empty($dead_urls)){
            $this->sendMail($dead_urls);
            $app = app('wechat.official_account');

            $urls= json_encode($dead_urls);
            $app->template_message->send([
                'touser' => 'oUBP90uyUhKbhZSK-EIAP-aQOXD4',
                'template_id' => 'jagxKqe1Yn90Ex5dXvdVWYg0R5vT8pZN-wv_b2Y-ylg',
                'url' => 'https://www.baidu.com',
                'data' => [
                    'first' => [
                        "value"=>'入口链接死亡:'.$urls
                    ],
                    'performance' =>[
                        "value"=>'请及时补充入口链接'
                    ],
                    'time'=>[
                        "value"=>date('Y-m-d H:i:s',time())
                    ],
                    'remark' => [
                        "value"=>'请及时补充入口链接'
                    ],

                 ],
            ]);
        }


    }

    public function sendMail($dead_urls)
    {

            Mail::to([
                'jingfengshi@kooap.com',
                'konglingyan@kooap.com',
            ])->send(new InformateSpreadDomainDead($dead_urls));

    }

}