<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\OpenId;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function blockOpenId($open_id)
    {
        OpenId::where('open_id',$open_id)->update(['block'=>1]);
    }



    public function blockIp($ip)
    {
        Location::where('ip',$ip)->update(['block'=>1]);
    }


    public function openOpenId($open_id)
    {
        OpenId::where('open_id',$open_id)->update(['block'=>0]);
    }

    public function openIp($ip)
    {
        Location::where('ip',$ip)->update(['block'=>0]);
    }
}
