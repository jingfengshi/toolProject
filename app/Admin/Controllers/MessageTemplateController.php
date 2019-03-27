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

        $grid->id('Id');
        $grid->gh_id('Gh id');
        $grid->type('Type');
        $grid->content('Content');
        $grid->media_id('Media id');
        $grid->title('Title');
        $grid->description('Description');
        $grid->url('Url');
        $grid->thumb_url('Thumb url');
        $grid->pagepath('Pagepath');
//        $grid->created_at('Created at');
//        $grid->updated_at('Updated at');
        $grid->status('启用')->display(
            function ($status) {
                if ($status) {
                    return '是';
                } else {
                    return '否';
                }
            }
        );
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

        $form->text('gh_id', 'Gh id')->required(true);

        $options=array('text'=>'文本消息', 'image'=>'图片消息', 'link'=>'图文链接', 'miniprogrampage'=>'卡片消息');
        $form->select('type', 'Type')->options($options)->required(true);

        $form->text('content', 'Content');
        $form->text('media_id', 'Media id');
        $form->text('title', 'Title');
        $form->text('description', 'Description');
        $form->url('url', 'Url');
        $form->text('thumb_url', 'Thumb url');
        $form->text('pagepath', 'Pagepath');
        $form->switch('status', '启用');

        return $form;
    }
}
