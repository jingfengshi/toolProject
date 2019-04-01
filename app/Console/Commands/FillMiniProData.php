<?php

namespace App\Console\Commands;

use App\Models\DailySummary;
use EasyWeChat\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FillMiniProData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fillminiprodata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '填充小程序数据分析';

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
        $select = ['gh_id', 'appid', 'appsecret', 'token', 'aeskey'];
        $data = DB::table('wechat_applet')->select($select)->get();
        if ($data->isNotEmpty()) {
            foreach ($data as $value) {
                $value = get_object_vars($value);
                $config = [
                    'app_id' => trim($value['appid']),
                    'secret' => trim($value['appsecret']),
                    'token' => trim($value['token']),
                    'aes_key' => trim($value['aeskey']),
                ];
                Log::info('$config', $config);
                $app = Factory::miniProgram($config);
                $this->getAnalysisDailySummary($app, $value['gh_id']);
            }
        }
    }

    /**
     * 获取用户访问小程序数据概况
     * @param $app
     * @param $gh_id
     */
    private function getAnalysisDailySummary($app, $gh_id)
    {
        $dateStr = date("Ymd", strtotime("-1 day"));
        $result = $app->data_cube->summaryTrend($dateStr, $dateStr);
        Log::info('$result:', $result);
        if ($result && $result['list'] && $result['list'][0]) {
            $insertData = $result['list'][0];
            $insertData['gh_id'] = $gh_id;
            DailySummary::updateOrCreate(['gh_id' => $gh_id], $insertData);
        }
    }

    /**
     * 获取用户访问小程序数据日趋势
     * @param $app
     * @param $gh_id
     */
    private function getAnalysisDailyVisitTrend($app, $gh_id)
    {
        $dateStr = date("Ymd", strtotime("-1 day"));
        $result = $app->data_cube->dailyVisitTrend($dateStr, $dateStr);
        Log::info('$result:', $result);
        if ($result && $result['list'] && $result['list'][0]) {
            $insertData = $result['list'][0];
            DailySummary::updateOrCreate(['gh_id' => $gh_id], $insertData);
        }
    }
}
