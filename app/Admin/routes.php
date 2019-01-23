<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    //工具管理
    $router->get('tools', 'ToolsController@shortDomain');
    $router->post('tools', 'ToolsController@store');

    $router->get('compress', 'CompressSitesController@index');
    $router->post('compress', 'CompressSitesController@store');


    //公众号管理
    $router->get('wechat/taskMessage', 'WeChatTaskMessageController@index');
    $router->post('wechat/taskMessage', 'WeChatTaskMessageController@store');


    //新增文字
    $router->get('wechat/wenzi', 'WeChatTaskMessageController@createWenzi');
    $router->post('wechat/wenzi', 'WeChatTaskMessageController@wenziStore');

    //编辑文字
    $router->get('wechat/wenzi/{id}/edit', 'WeChatTaskMessageController@editWenzi');
    $router->put('wechat/wenzi/{id}', 'WeChatTaskMessageController@updateWenzi');

    //新增图文
    $router->get('wechat/tuwen', 'WeChatTaskMessageController@createTuwen');
    $router->post('wechat/tuwen', 'WeChatTaskMessageController@tuwenStore');

    $router->get('wechat/tuwen/{id}/edit', 'WeChatTaskMessageController@editTuwen');
    $router->put('wechat/tuwen/{id}', 'WeChatTaskMessageController@updateTuwen');


    //新增图片
    $router->get('wechat/image', 'WeChatTaskMessageController@createImage');
    $router->post('wechat/image', 'WeChatTaskMessageController@imageStore');

    //编辑图片
    $router->get('wechat/image/{id}/edit', 'WeChatTaskMessageController@editImage');
    $router->put('wechat/image/{id}', 'WeChatTaskMessageController@updateImage');

    //删除消息
    $router->delete('wechat/taskMessage/{id}', 'WeChatTaskMessageController@destroy');
    //修改消息状态
    $router->put('wechat/taskMessage/status/{id}', 'WeChatTaskMessageController@status');

});
