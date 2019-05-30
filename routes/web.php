<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Text;

Route::get('/', function () {

    if (isset($_GET['url'])) {
        $urlRaw = strip_tags($_GET['url']);
        $urlRaw = explode('&', base64_decode($urlRaw));
        var_dump($urlRaw);

        $url = $urlRaw[0];
        $time = strtotime($urlRaw[1]);
        if (time() > $time) {
            echo '对不起，网页已失效';
            exit();
        }

        header('location:'.$url);
    }
});


Route::get('wechat_image', function () {
    header('Access-Control-Allow-Origin: *');
    $url = $_GET['url'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    //curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($form));
    curl_setopt($ch, CURLOPT_HEADER, false);

    $res = curl_exec($ch);

    // 关闭cURL资源，并且释放系统资源
    curl_close($ch);

    if (stripos($url, 'v.qq.com') !== false) {
        echo $url;
    } else {
        $image = $res;
        header('Content-type:image/jpeg');
        echo $image;
    }

});

Route::any('/wechat', 'WeChatController@serve');
Route::any('/qrcode', function(){
    $app = app('wechat.mini_program');
});

//入口域名
Route::get('/spread/{spread}','SpreadController@index');
//授权域名
Route::get('/spread/{spread}','AuthDomainsController@index')->middleware(['getLocation','getOpenId']);
//落地域名
Route::get('/rtyythggfghssdfxzvcdfghdhgfdhewqsdf/{num}/{spread}','LandController@index');
Route::get('/apply_back','ApplyBackController@index');
Route::get('/err','ApplyBackController@err');

Route::get('/wechatRedirect','WeChatRediectController@getCode');


//拉黑
Route::get('/blockOpenId/{open_id}','BlockController@blockOpenId');
Route::get('/blockIp/{ip}','BlockController@blockIp');

//拉白
Route::get('/openOpenId/{open_id}','BlockController@openOpenId');
Route::get('/openIp/{ip}','BlockController@openIp');




