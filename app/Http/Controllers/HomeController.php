<?php

namespace App\Http\Controllers;

use App\Models\GameBanner;
use App\Models\GameType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function getHomeDatas()
    {
        $banner = $this->getGameBanners();
        $xcxList = $this->getGameDatas();
        $data = $this->extraDatas();
        $data['data']['banner'] = $banner;
        $data['data']['xcxList'] = $xcxList;
        return response()->json($data);
    }

    public function getGameDatas()
    {
        $arr = ['id', 'type', 'typeValue', 'typeLogo'];
        $data = GameType::all($arr);
        foreach ($data as $key => $value) {
            if ($value['type']) {
                $gameDatas = $this->getGamesByType($value['type']);
                if (!$gameDatas->isEmpty()) {
                    $value['lists'] = $gameDatas;
                } else {
                    continue;
                }
            } else {
                continue;
            }
        }
        return $data;
    }

    /**
     * 根据游戏$typeId获取数据
     * @param $typeId
     * @return \Illuminate\Database\Query\Builder
     */
    public function getGamesByType($typeId)
    {
        $gameArr = ['location_index', 'id', 'ghId', 'typeId', 'jumpId', 'jumpAppId', 'clickNub', 'introduce', 'logo'
            , 'jumpName', 'aliasName', 'jumpType', 'extraData', 'jumpGhId'];
        $gameDatas = DB::table('games')->where('type', $typeId)->select($gameArr)->get();
        return $gameDatas;
    }

    /**
     * 获取首页banner数据
     * @return GameBanner[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getGameBanners()
    {
        $arr = ['id', 'url', 'jumpId', 'ghId', 'jumpUrl', 'jumpAppId', 'bannerUrl', 'introduce', 'jumpType', 'jumpName', 'display', 'created_at'
            , 'extraData', 'clickNub', 'logo', 'clickRate', 'tabLogo', 'sort', 'jumpGhId'];
        $data = GameBanner::all($arr);
        return $data;
    }

    /**
     * 额外数据
     * @return array
     */
    public function extraDatas()
    {
        $insideData = [
            'aliPayCode' => 'nothing',
            'contact' => 'nothing',
            'shareLogo' => 'nothing',
            'qRCodeLogo' => 'https://api.dafanjia.com/chao_hui_wan_tui_jian.png',
            'followLogo' => 'http://p8c9lk5xz.bkt.clouddn.com/%E7%BB%843@2x.png',
            'xcxShare' => 'nothing',
            'showAdvert' => 0,
            'advertValue' => 'nothing',
            'xcxTitle' => '游戏盒子'
        ];
        $data = [
            'code' => 100,
            'msg' => '操作成功',
            'data' => $insideData
        ];
        return $data;
    }
}
