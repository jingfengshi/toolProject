<?php

namespace App\Http\Controllers;

use App\Models\AuthDomain;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpreadController extends Controller
{
    public function index($spread)
    {
        if(AuthDomain::where('is_dead',false)->exists()){

            $landUrl ='http://auth.'.AuthDomain::where('is_dead',false)->orderBy(DB::raw('RAND()'))->take(1)->value('url');
            $final_url =$landUrl.'/'.$spread;
            header('Location: '.$final_url);//
        }else{
            header('Location: http://baidu.com');//如果没有落地域名则跳转到百度
        }

    }
}
