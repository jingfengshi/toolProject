<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameFakerController extends Controller
{

    public function show(Request $request)
    {

        $appletId = $request->appletId;

        $arr = ['id', 'appletId', 'banner_images', 'content_images'];
        $data = DB::table('game_fakers')->where('appletId', $appletId)->select($arr)->get();
        return response()->json($data);

    }

}