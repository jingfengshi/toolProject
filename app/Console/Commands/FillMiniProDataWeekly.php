<?php

namespace App\Console\Commands;

use App\Models\WeeklyVisitTrend;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\HttpException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FillMiniProDataWeekly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fillminiprodataweekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '填充小程序数据分析周数据';

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
        $where = ['status' => 1];
        $data = DB::table('wechat_applet')->select($select)->where($where)->get();
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
                try {
                    $this->getAnalysisWeeklyVisitTrend($app, $value['gh_id']);
                } catch (HttpException $httpException) {
                    Log::error($httpException->getMessage());
                } catch (\Exception $exception) {
                    Log::error($exception->getMessage());
                }
            }
        }
    }

    /**
     * 获取用户访问小程序数据周趋势
     * @param $app
     * @param $gh_id
     */
    private function getAnalysisWeeklyVisitTrend($app, $gh_id)
    {
        $lastMonday = date('Ymd', strtotime('-2 monday', time()));
        $lastSunday = date('Ymd', strtotime('-1 sunday', time()));
        $result = $app->data_cube->weeklyVisitTrend($lastMonday, $lastSunday);
        Log::info('$result:', $result);
        if ($result && isset($result['list']) && $result['list'] && $result['list'][0]) {
            $insertData = $result['list'][0];
            $insertData['gh_id'] = $gh_id;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = $lastMonday . '-' . $lastSunday;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
        }
        WeeklyVisitTrend::insert($insertData);
    }
}
