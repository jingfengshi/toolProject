<?php

namespace App\Console\Commands;

use App\Models\DailySummary;
use App\Models\DailyVisitTrend;
use App\Models\MonthlyVisitTrend;
use App\Models\WeeklyVisitTrend;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\HttpException;
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
                Log::info('FillMiniProData' . $value['name'] . ' config:', $config);
                $app = Factory::miniProgram($config);
                try {
                    $dateStr = date("Ymd", strtotime("-1 day"));
                    $this->getAnalysisDailySummary($app, $value['gh_id'], $dateStr);
                    $this->getAnalysisDailyVisitTrend($app, $value['gh_id'], $dateStr);
                    $this->getVisitPage($app, $value['gh_id'], $dateStr);
                    $this->getUserPortrait($app, $value['gh_id'], $dateStr);
                } catch (HttpException $httpException) {
                    Log::error($httpException->getMessage());
                } catch (\Exception $exception) {
                    Log::error($exception->getMessage());
                }
            }
        }
    }

    /**
     * 获取用户访问小程序数据概况
     * @param $app
     * @param $gh_id
     */
    private function getAnalysisDailySummary($app, $gh_id, $dateStr)
    {
        $result = $app->data_cube->summaryTrend($dateStr, $dateStr);
        Log::info('FillMiniProData' . 'getAnalysisDailySummary result:', $result);
        if ($result && isset($result['list']) && $result['list'] && $result['list'][0]) {
            $insertData = $result['list'][0];
            $insertData['gh_id'] = $gh_id;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
        }
        DailySummary::updateOrCreate(['gh_id' => $gh_id], $insertData);
    }

    /**
     * 获取用户访问小程序数据日趋势
     * @param $app
     * @param $gh_id
     */
    private function getAnalysisDailyVisitTrend($app, $gh_id, $dateStr)
    {
        $result = $app->data_cube->dailyVisitTrend($dateStr, $dateStr);
        Log::info('FillMiniProData' . 'getAnalysisDailyVisitTrend result:', $result);
        if ($result && isset($result['list']) && $result['list'] && $result['list'][0]) {
            $insertData = $result['list'][0];
            $insertData['gh_id'] = $gh_id;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
        }
        DailyVisitTrend::insert($insertData);
    }

    /**
     * 获取用户访问小程序数据访问页面
     * @param $app
     * @param $gh_id
     */
    private function getVisitPage($app, $gh_id, $dateStr)
    {
        $result = $app->data_cube->visitPage($dateStr, $dateStr);
        Log::info('FillMiniProData' . 'getVisitPage result:', $result);
        if ($result && isset($result['list']) && $result['list'] && $result['list'][0]) {
            foreach ($result['list'] as $data) {
                $insertData = $data;
                $insertData['gh_id'] = $gh_id;
                $insertData['ref_date'] = $dateStr;
                $insertData['updated_at'] = date('Y-m-d H:i:s');
                DB::table('visit_page')->insert($insertData);
            }
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            DB::table('visit_page')->insert($insertData);
        }
    }

    /**
     * 获取用户访问小程序数据用户分布
     * @param $app
     * @param $gh_id
     */
    private function getUserPortrait($app, $gh_id, $dateStr)
    {
        $result = $app->data_cube->userPortrait($dateStr, $dateStr);
        Log::info('FillMiniProData' . 'getUserPortrait result:', $result);
        $this->getUserPortraitGenderNew($result, $gh_id, $dateStr);
        $this->getUserPortraitGenderPlatforms($result, $gh_id, $dateStr);
        $this->getUserPortraitGenderAges($result, $gh_id, $dateStr);
        $this->getUserPortraitGenderUV($result, $gh_id, $dateStr);
        $this->getUserPortraitGenderPlatformsUV($result, $gh_id, $dateStr);
        $this->getUserPortraitGenderAgesUV($result, $gh_id, $dateStr);
    }

    /**
     * 获取用户访问小程序数据新增用户性别分布
     * @param $result
     * @param $gh_id
     * @param $dateStr
     */
    private function getUserPortraitGenderNew($result, $gh_id, $dateStr)
    {
        if ($result && isset($result['visit_uv_new']['genders']) && $result['visit_uv_new']['genders']) {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            foreach ($result['visit_uv_new']['genders'] as $data) {
                switch ($data['name']) {
                    case '未知':
                        $insertData['unknown'] = $data['value'];
                        break;
                    case '男':
                        $insertData['male'] = $data['value'];
                        break;
                    case '女':
                        $insertData['female'] = $data['value'];
                        break;
                }
            }
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            DB::table('wechat_user_portrait_gender')->insert($insertData);
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            DB::table('wechat_user_portrait_gender')->insert($insertData);
        }
    }

    /**
     * 获取用户访问小程序数据新增用户平台分布
     * @param $result
     * @param $gh_id
     * @param $dateStr
     */
    private function getUserPortraitGenderPlatforms($result, $gh_id, $dateStr)
    {
        if ($result && isset($result['visit_uv_new']['platforms']) && $result['visit_uv_new']['platforms']) {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            Log::info('visit_uv_new_platforms:', $result['visit_uv_new']['platforms']);
            foreach ($result['visit_uv_new']['platforms'] as $data) {
                switch ($data['name']) {
                    case '其他':
                        $insertData['other'] = $data['value'];
                        break;
                    case 'iPhone':
                        $insertData['iphone'] = $data['value'];
                        break;
                    case 'Android':
                        $insertData['android'] = $data['value'];
                        break;
                }
            }
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            Log::info('$insertData:', $insertData);
            DB::table('wechat_user_portrait_platforms')->insert($insertData);
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            DB::table('wechat_user_portrait_platforms')->insert($insertData);
        }
    }

    /**
     * 获取用户访问小程序数据新增用户年龄分布
     * @param $result
     * @param $gh_id
     * @param $dateStr
     */
    private function getUserPortraitGenderAges($result, $gh_id, $dateStr)
    {
        if ($result && isset($result['visit_uv_new']['ages']) && $result['visit_uv_new']['ages']) {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            Log::info('visit_uv_new_platforms:', $result['visit_uv_new']['platforms']);
            foreach ($result['visit_uv_new']['ages'] as $data) {
                switch ($data['name']) {
                    case '未知':
                        $insertData['unknown'] = $data['value'];
                        break;
                    case '17岁以下':
                        $insertData['under17'] = $data['value'];
                        break;
                    case '18-24岁':
                        $insertData['age18_24'] = $data['value'];
                        break;
                    case '25-29岁':
                        $insertData['age25_29'] = $data['value'];
                        break;
                    case '30-39岁':
                        $insertData['age30_39'] = $data['value'];
                        break;
                    case '40-49岁':
                        $insertData['age40_49'] = $data['value'];
                        break;
                    case '50岁以上':
                        $insertData['over50'] = $data['value'];
                        break;
                }
            }
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            Log::info('$insertData:', $insertData);
            DB::table('wechat_user_portrait_ages')->insert($insertData);
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            DB::table('wechat_user_portrait_ages')->insert($insertData);
        }
    }

    /**
     * 获取用户访问小程序数据活跃用户性别分布
     * @param $result
     * @param $gh_id
     * @param $dateStr
     */
    private function getUserPortraitGenderUV($result, $gh_id, $dateStr)
    {
        if ($result && isset($result['visit_uv']['genders']) && $result['visit_uv']['genders']) {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            foreach ($result['visit_uv']['genders'] as $data) {
                switch ($data['name']) {
                    case '未知':
                        $insertData['unknown'] = $data['value'];
                        break;
                    case '男':
                        $insertData['male'] = $data['value'];
                        break;
                    case '女':
                        $insertData['female'] = $data['value'];
                        break;
                }
            }
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            DB::table('wechat_user_portrait_genderuv')->insert($insertData);
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            DB::table('wechat_user_portrait_genderuv')->insert($insertData);
        }
    }

    /**
     * 获取用户访问小程序数据活跃用户平台分布
     * @param $result
     * @param $gh_id
     * @param $dateStr
     */
    private function getUserPortraitGenderPlatformsUV($result, $gh_id, $dateStr)
    {
        if ($result && isset($result['visit_uv']['platforms']) && $result['visit_uv']['platforms']) {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            foreach ($result['visit_uv']['platforms'] as $data) {
                switch ($data['name']) {
                    case '其他':
                        $insertData['other'] = $data['value'];
                        break;
                    case 'iPhone':
                        $insertData['iphone'] = $data['value'];
                        break;
                    case 'Android':
                        $insertData['android'] = $data['value'];
                        break;
                }
            }
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            Log::info('$insertData:', $insertData);
            DB::table('wechat_user_portrait_platformsuv')->insert($insertData);
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            DB::table('wechat_user_portrait_platformsuv')->insert($insertData);
        }
    }

    /**
     * 获取用户访问小程序数据活跃用户年龄分布
     * @param $result
     * @param $gh_id
     * @param $dateStr
     */
    private function getUserPortraitGenderAgesUV($result, $gh_id, $dateStr)
    {
        if ($result && isset($result['visit_uv']['ages']) && $result['visit_uv']['ages']) {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            foreach ($result['visit_uv']['ages'] as $data) {
                switch ($data['name']) {
                    case '未知':
                        $insertData['unknown'] = $data['value'];
                        break;
                    case '17岁以下':
                        $insertData['under17'] = $data['value'];
                        break;
                    case '18-24岁':
                        $insertData['age18_24'] = $data['value'];
                        break;
                    case '25-29岁':
                        $insertData['age25_29'] = $data['value'];
                        break;
                    case '30-39岁':
                        $insertData['age30_39'] = $data['value'];
                        break;
                    case '40-49岁':
                        $insertData['age40_49'] = $data['value'];
                        break;
                    case '50岁以上':
                        $insertData['over50'] = $data['value'];
                        break;
                }
            }
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            DB::table('wechat_user_portrait_agesuv')->insert($insertData);
        } else {
            $insertData = [];
            $insertData['gh_id'] = $gh_id;
            $insertData['ref_date'] = $dateStr;
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            DB::table('wechat_user_portrait_agesuv')->insert($insertData);
        }
    }


}
