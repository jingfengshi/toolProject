<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WeChatRediectController extends Controller
{
    public function getCode()
    {
        $code = request('code');
        Log::error('11111');
        Log::error($code);
    }
}
