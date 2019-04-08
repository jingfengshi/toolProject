<?php

namespace App\Console\Commands;

use App\Models\MonthlyVisitTrend;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\HttpException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FillMiniProDataMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fillminiprodatamonthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '填充小程序数据分析月数据';

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
        $select = ['gh_id', 'appid', 'appsecret', 'token', 'aeskey', 'name'];
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
                Log::info($value['name'] . '$config', $config);
                $app = Factory::miniProgram($config);
                try {
                    $this->getMonthlyVisitTrend($app, $value['gh_id']);
                } catch (HttpException $httpException) {
                    Log::error($httpException->getMessage());
                } catch (\Exception $exception) {
                    Log::error($exception->getMessage());
                }
            }
        }
    }

    /**
     * 获取用户访问小程序数据月趋势
     * @param $app
     * @param $gh_id
     */
    private function getMonthlyVisitTrend($app, $gh_id)
    {
        $beginDate = date('Ymd', strtotime('-1 month', strtotime(date('Y-m', time()) . '-01 00:00:00')));
        $endDate = date('Ymd', strtotime(date('Y-m', time()) . '-01 00:00:00') - 86400);
        $result = $app->data_cube->monthlyVisitTrend($beginDate, $endDate);
        Log::info('FillMiniProDataMonthly$getMonthlyVisitTrend$result:', $result);
        if ($result && isset($result['list']) && $result['list'] && $result['list'][0]) {
            $insertData = $result['list'][0];
            $insertData['gh_id'] = $gh_id;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = date('Ym', strtotime('-1 month'));
            $insertData['updated_at'] = date('Y-m-d H:i:s');
        }
        MonthlyVisitTrend::insert($insertData);
    }
}
