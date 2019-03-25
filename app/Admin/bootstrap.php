<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use Encore\Admin\Admin;

Encore\Admin\Form::forget(['map', 'editor']);

Admin::js('/vendor/jcrop/js/jquery.color.js');
Admin::js('/vendor/jcrop/js/jquery.Jcrop.js');
Admin::css('/vendor/jcrop/css/jquery.Jcrop.css');


Admin::js('/vendor/layui-v2.4.5/layui/layui.js');
Admin::css('/vendor/layui-v2.4.5/layui/css/layui.css');