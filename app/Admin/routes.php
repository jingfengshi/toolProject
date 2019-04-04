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


    //聚合页
    $router->get('juhe', 'JuheController@index');
    $router->get('juhe/create', 'JuheController@create');
    $router->post('juhe', 'JuheController@store');
    $router->delete('juhe/{id}', 'JuheController@destroy');


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

    //游戏过审状态
    $router->get('wechatapplet', 'WechatAppletController@index');
    $router->post('wechatapplet', 'WechatAppletController@store');
    $router->get('wechatapplet/create', 'WechatAppletController@create');
    $router->get('wechatapplet/{id}', 'WechatAppletController@show');
    $router->get('wechatapplet/{id}/edit', 'WechatAppletController@edit');
    $router->put('wechatapplet/{id}', 'WechatAppletController@update');
    $router->delete('wechatapplet/{id}', 'WechatAppletController@destroy');

    //游戏攻略
    $router->get('gamestrategy', 'GameStrategyController@index');
    $router->post('gamestrategy', 'GameStrategyController@store');
    $router->get('gamestrategy/create', 'GameStrategyController@create');
    $router->get('gamestrategy/{id}', 'GameStrategyController@show');
    $router->get('gamestrategy/{id}/edit', 'GameStrategyController@edit');
    $router->put('gamestrategy/{id}', 'GameStrategyController@update');
    $router->delete('gamestrategy/{id}', 'GameStrategyController@destroy');

    //游戏banner图片
    $router->get('gamebanner', 'GameBannerController@index');
    $router->post('gamebanner', 'GameBannerController@store');
    $router->get('gamebanner/create', 'GameBannerController@create');
    $router->get('gamebanner/{id}', 'GameBannerController@show');
    $router->get('gamebanner/{id}/edit', 'GameBannerController@edit');
    $router->put('gamebanner/{id}', 'GameBannerController@update');
    $router->delete('gamebanner/{id}', 'GameBannerController@destroy');

    //游戏分类
    $router->get('gametype', 'GameTypeController@index');
    $router->post('gametype', 'GameTypeController@store');
    $router->get('gametype/create', 'GameTypeController@create');
    $router->get('gametype/{id}', 'GameTypeController@show');
    $router->get('gametype/{id}/edit', 'GameTypeController@edit');
    $router->put('gametype/{id}', 'GameTypeController@update');
    $router->delete('gametype/{id}', 'GameTypeController@destroy');

    //游戏详情
    $router->get('games', 'GamesController@index');
    $router->post('games', 'GamesController@store');
    $router->get('games/create', 'GamesController@create');
    $router->get('games/{id}', 'GamesController@show');
    $router->get('games/{id}/edit', 'GamesController@edit');
    $router->put('games/{id}', 'GamesController@update');
    $router->delete('games/{id}', 'GamesController@destroy');

    //消息管理
    $router->get('messagetemplate', 'MessageTemplateController@index');
    $router->post('messagetemplate', 'MessageTemplateController@store');
    $router->get('messagetemplate/create', 'MessageTemplateController@create');
    $router->get('messagetemplate/{id}', 'MessageTemplateController@show');
    $router->get('messagetemplate/{id}/edit', 'MessageTemplateController@edit');
    $router->put('messagetemplate/{id}', 'MessageTemplateController@update');
    $router->delete('messagetemplate/{id}', 'MessageTemplateController@destroy');

    //小程序内容管理
    $router->get('wechatcontent', 'WechatContentController@index');
    $router->post('wechatcontent', 'WechatContentController@store');
    $router->get('wechatcontent/create', 'WechatContentController@create');
    $router->get('wechatcontent/{id}', 'WechatContentController@show');
    $router->get('wechatcontent/{id}/edit', 'WechatContentController@edit');
    $router->put('wechatcontent/{id}', 'WechatContentController@update');
    $router->delete('wechatcontent/{id}', 'WechatContentController@destroy');

    //微信短连接
    $router->get('wxshortdomain', 'WxShortDomainController@wxShortDomain');
    $router->post('wxshortdomain', 'WxShortDomainController@store');



    //图片处理
    $router->get('/template/allTemplates','TemplatesController@allTemplates');
    $router->resource('/template','TemplatesController');

    $router->get('/poster/download','PosterController@download');
    $router->get('/poster/download_package/{id}','PosterController@downloadPackage');


    $router->get('/codes/download_package/{id}','CodesController@downloadPackage');

    $router->resource('/poster','PosterController');

    $router->resource('/code','CodesController');

    //域名购买
    $router->get('domainregistration', 'DomainRegistrationController@domainRegistration');
    $router->post('domainregistration', 'DomainRegistrationController@store');




    $router->resource('/gameFaker','GameFakersController');



    //小程序数据
    //概况
    $router->get('dailysummary', 'DailySummaryController@index');
    //日趋势
    $router->get('dailyvisittrend', 'DailyVisitTrendController@index');
    //周趋势
    $router->get('weeklyvisittrend', 'WeeklyVisitTrendController@index');
    //月趋势
    $router->get('monthlyvisittrend', 'MonthlyVisitTrendController@index');
    //访问页面
    $router->get('visitpage', 'VisitPageController@index');

    //新用户画像-性别
    $router->get('userportraitgendernew', 'UserPortraitGenderController@index');
    //新用户画像-平台
    $router->get('userportraitplatformsnew', 'UserPortraitPlatformsController@index');
    //新用户画像-年龄
    $router->get('userportraitagesnew', 'UserPortraitAgesController@index');

    //活跃用户画像-性别
    $router->get('userportraitgenderuv', 'UserPortraitGenderUVController@index');
    //新用户画像-平台
    $router->get('userportraitplatformsuv', 'UserPortraitPlatformsUVController@index');
    //新用户画像-年龄
    $router->get('userportraitagesuv', 'UserPortraitAgesUVController@index');
});
