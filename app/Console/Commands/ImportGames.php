<?php

namespace App\Console\Commands;

use App\Models\GameBanner;
use App\Models\Games;
use App\Models\GameType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:importgames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入游戏数据';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = 'https://hundunxcx.com:808/v3/xcx/jump/get/all';
        $data = '{"ghId":"gh_4df546c7e919"}';
        $arr = $this->postUrlData($url, $data);

        $this->insertDatas($arr);

    }

    /**
     * 获取post请求数据
     * @param $url
     * @param $params
     * @return mixed
     */
    public function postUrlData($url, $params)
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
     * 插入获取的数据
     * @param $data
     */
    function insertDatas($data)
    {
        $banner = $data['data']['banner'];
        $xcxList = $data['data']['xcxList'];
        //清空三张表
        GameBanner::truncate();
        GameType::truncate();
        Games::truncate();

        //插入轮播图表数据
        foreach ($banner as $key=>$value) {
            unset($value['id']);
            unset($value['createTime']);
            DB::table('game_banner')->insert($value);
        }

        //插入游戏及分类数据
        foreach ($xcxList as $key=>$value) {
            $lists = $value['lists'];
            switch ($value['typeValue']){
                case '小游戏精选':
                    $gametype = 1;
                    break;
                case '热门小游戏':
                    $gametype = 2;
                    break;
                case '独家代理':
                    $gametype = 3;
                    break;
                case '热门游戏推荐':
                    $gametype = 4;
                    break;
            }

            foreach ($lists as $k=>$v) {
                unset($v['id']);
                unset($v['jumpUrl']);
                $v['type'] = $gametype;
                DB::table('games')->insert($v);
            }

            unset($value['lists']);
            unset($value['id']);
            $value['typeValue'] = $gametype;
            DB::table('game_type')->insert($value);
        }
    }
}
