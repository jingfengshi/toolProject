<?php

namespace App\Admin\Controllers;

use App\Models\WechatContent;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WechatContentController extends Controller
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
            ->header('Index')
            ->description('小程序内容')
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
            ->header('Detail')
            ->description('description')
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
            ->header('Edit')
            ->description('description')
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
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WechatContent);

//        $grid->id('Id');
//        $grid->appid('Appid');
        $grid->alias('别名');
//        $grid->domain('Domain');
        $grid->url('内容网址');
        $grid->imgUrl('内容图片');
//        $grid->created_at('Created at');
//        $grid->updated_at('Updated at');

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
        });
        $grid->disableCreateButton();
        $grid->disablePagination();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->disableRowSelector();


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
        $show = new Show(WechatContent::findOrFail($id));

        $show->id('Id');
        $show->appid('Appid');
        $show->alias('Alias');
        $show->domain('Domain');
        $show->url('Url');
        $show->imgUrl('ImgUrl');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WechatContent);

        $form->hidden('appid', 'Appid');
        $form->text('alias', '别名');
        $form->hidden('domain', 'Domain');
        $form->url('url', '内容网址');
//        $form->text('imgUrl', 'ImgUrl');
        $form->image('imgUrl', '内容图片')->uniqueName();

        $form->disableEditingCheck();

        $form->disableCreatingCheck();

        $form->disableViewCheck();

        return $form;
    }
}
