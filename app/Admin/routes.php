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
});
