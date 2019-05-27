<?php

namespace App\Http\Controllers;

use App\Models\LandDomain;
use Illuminate\Http\Request;

class SpreadController extends Controller
{
    public function index($spread)
    {
        if(LandDomain::where('is_dead',false)->exists()){
            dd(request('open_id'));
            $landUrl ='http://'.LandDomain::where('is_dead',false)->first()->value('url');
            $final_url =$landUrl.'/rtyythggfghssdfxzvcdfghdhgfdhewqsdf/'.rand(111111111,999999999).'/'.$spread.'?open_id=';
            header('Location: '.$final_url);//
        }else{
            header('Location: http://baidu.com');//如果没有落地域名则跳转到百度
        }

    }
}
