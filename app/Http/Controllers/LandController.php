<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandController extends Controller
{
    public function index($number,$spread)
    {

        $open_id = request('openid');

        $origin_url = base64_decode($spread);

        return view('land',compact('origin_url','open_id'));
    }
}
