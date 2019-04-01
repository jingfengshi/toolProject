<?php

namespace App\Admin\Controllers;

use App\Models\WechatApplet;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WechatAppletController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('过审')
            ->description('过审状态')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('过审')
            ->description('过审状态')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('过审状态')
            ->description('过审状态')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('过审状态')
            ->description('过审状态')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WechatApplet);

        $grid->id('Id');
        $grid->gh_id('Ghid');
        $grid->appid('Appid');
//        $grid->appsecret('Appsecret');
//        $grid->aeskey('Aeskey');
//        $grid->token('Token');

        $grid->name('名字');
        $grid->alias('别名');
//        $grid->domain('域名');
        $grid->status('过审')->display(
            function ($status) {
                if ($status) {
                    return '是';
                } else {
                    return '否';
                }
            }
        );
        $grid->expandFilter();
//        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('name', '小程序名字');
//            $filter->scope('status', '已过审')->where('status', 1);
//            $filter->scope('status', '未过审')->where('status', 1);
//            $filter->equal('status', '过审状态')->select([0 => '未过审', 1=>'已过审']);

            $filter->equal('status', '过审状态')->radio([
                '' => '所有',
                0    => '未过审',
                1    => '已过审',
            ]);

        });


        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WechatApplet::findOrFail($id));

        $show->gh_id('Ghid');
        $show->appid('Appid');
        $show->appsecret('Appsecret');
        $show->aeskey('Aeskey');
        $show->token('Token');
        $show->alias('别名');
        $show->domain('域名');
        $show->name('名字');
        $show->status('过审')->as(
            function ($status) {
                if ($status) {
                    return '是';
                } else {
                    return '否';
                }
            }
        );
//        $show->created_at('Created at');
//        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WechatApplet);

        $form->text('gh_id', 'Ghid')->required(true);
        $form->text('appid', 'Appid')->required(true);
        $form->text('appsecret', 'Appsecret')->required(true);
        $form->text('aeskey', 'Aeskey')->required(true);
        $form->text('token', 'Token')->required(true);
        $form->text('name', '名字')->required(true);
        $form->text('alias', '别名')->required(true);
        $form->text('domain', '域名')->required(true)->default('https://toolproject.jinhuyingke03.com');
        $form->switch('status', 'Status');

        return $form;
    }
}
