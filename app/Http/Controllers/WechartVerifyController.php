<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WechartVerifyController extends Controller
{
    //用于小程序后台第一步验证返回，验证成功后便可注释
    public function valid()
    {
//        $appid = 'wxbb67ce9bccb96eb3';
//        $AppSecret = ' 26746350f5fd21c1a0d14d8f231dee6f';
        $echoStr = $_GET["echostr"];
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    //官方提供的验证demo
    public function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = 'flybird';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
}
