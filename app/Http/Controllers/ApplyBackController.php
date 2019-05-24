<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplyBackController extends Controller
{
    public function index()
    {
        return view('appBack');
    }


    public function err()
    {
        return view('err');
    }
}
