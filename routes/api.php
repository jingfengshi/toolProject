<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//游戏攻略过审首页
Route::get('/gamestrategy','GameStrategyController@showlists');
Route::get('/gamestrategy/{appletId}','GameStrategyController@getItemByAppletId');

//游戏攻略首页
Route::get('/gamehome','HomeController@getHomeDatas');

//过审状态
Route::get('/wechatapplet','WechatAppletController@showLists');
Route::post('/wechatapplet/getitem','WechatAppletController@getItemByAppid');

//微信小程序
Route::get('/wechatverify','WechartVerifyController@valid');
