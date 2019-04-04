<?php

namespace App\Admin\Controllers;

use App\Models\MessageTemplate;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MessageTemplateController extends Controller
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
            ->header('消息')
            ->description('小程序消息')
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
            ->header('消息')
            ->description('小程序消息')
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
            ->header('消息')
            ->description('小程序消息')
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
            ->header('消息')
            ->description('小程序消息')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MessageTemplate);

//        $grid->id('Id');
//        $grid->gh_id('Gh id');
//        $grid->type('Type');
//        $grid->content('Content');
//        $grid->media_id('Media id');
        $grid->title('标题');
        $grid->description('描述');
        $grid->url('跳转图片');
        $grid->thumb_url('缩略图');
//        $grid->pagepath('Pagepath');
//        $grid->created_at('Created at');
//        $grid->updated_at('Updated at');
//        $grid->status('启用')->display(
//            function ($status) {
//                if ($status) {
//                    return '是';
//                } else {
//                    return '否';
//                }
//            }
//        );
        $grid->actions(function ($actions) {
            $actions->disableDelete();
//            $actions->disableEdit();
            $actions->disableView();
        });
        $grid->disableCreateButton();
        $grid->disablePagination();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->disableRowSelector();
//        $grid->model()->orderBy('id', 'desc');
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
        $show = new Show(MessageTemplate::findOrFail($id));

        $show->id('Id');
        $show->gh_id('Gh id');
        $show->type('Type');
        $show->content('Content');
        $show->media_id('Media id');
        $show->title('Title');
        $show->description('Description');
        $show->url('Url');
        $show->thumb_url('Thumb url');
        $show->pagepath('Pagepath');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->status('启用')->as(
            function ($status) {
                if ($status) {
                    return '是';
                } else {
                    return '否';
                }
            }
        );

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MessageTemplate);

//        $form->text('gh_id', 'Gh id')->required(true);
        $form->hidden('gh_id');

//        $options=array('text'=>'文本消息', 'image'=>'图片消息', 'link'=>'图文链接', 'miniprogrampage'=>'卡片消息');
//        $form->select('type', 'Type')->options($options)->required(true);
        $form->hidden('type');

//        $form->text('content', 'Content');
        $form->hidden('content');
//        $form->text('media_id', 'Media id');
        $form->hidden('media_id');
        $form->text('title', '标题');
        $form->text('description', '描述');
//        $form->text('thumb_url', '缩略图');
        $form->image('thumb_url', '缩略图')->uniqueName();
//        $form->url('url', '跳转图片');
        $form->image('url', '跳转图片')->uniqueName();
//        $form->text('pagepath', 'Pagepath');
        $form->hidden('pagepath');
//        $form->switch('status', '启用');
        $form->hidden('status');

        $form->disableEditingCheck();

        $form->disableCreatingCheck();

        $form->disableViewCheck();

        return $form;
    }
}
