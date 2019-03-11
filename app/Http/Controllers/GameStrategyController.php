<?php

namespace App\Http\Controllers;

use App\Models\GameStrategy;
use Illuminate\Http\Request;

/**
 * 前台使用 游戏攻略
 * Class GameStrategyController
 * @package App\Http\Controllers
 */
class GameStrategyController extends Controller
{
    public function showlists() {
        $arr = ['appletId','titleImg','titleName','content'];
        $data = GameStrategy::all($arr);
        return response()->json($data);
    }
}
