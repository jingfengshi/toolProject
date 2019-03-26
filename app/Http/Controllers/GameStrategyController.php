<?php

namespace App\Http\Controllers;

use App\Models\GameStrategy;
use function EasyWeChat\Kernel\data_to_array;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 前台使用 游戏攻略
 * Class GameStrategyController
 * @package App\Http\Controllers
 */
class GameStrategyController extends Controller
{
    /**
     * 获取所有游戏攻略
     * @return \Illuminate\Http\JsonResponse
     */
    public function showlists()
    {
        $arr = ['id', 'appletId', 'titleImg', 'titleName', 'content'];
        $data = GameStrategy::all($arr);
        return response()->json($data);
    }

    /**
     *根据appletId获取单条数据
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItemById($id)
    {
        $arr = ['id', 'appletId', 'titleImg', 'titleName', 'content'];
        $data = DB::table('game_strategy')->where('id', $id)->select($arr)
            ->first();
        return response()->json($data);
    }

}
