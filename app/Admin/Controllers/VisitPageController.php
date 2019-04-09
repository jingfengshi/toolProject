<?php

namespace App\Admin\Controllers;

use App\Models\VisitPage;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class VisitPageController extends Controller
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
            ->description('访问页面')
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
            ->description('访问页面')
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
        $grid = new Grid(new VisitPage);

        $grid->id('Id');
        $grid->gh_id('Ghid');
        $grid->column('wechat_applet.name', '名字');
        $grid->ref_date('日期');
        $grid->page_path('页面路径');
        $grid->page_visit_pv('访问次数');
        $grid->page_visit_uv('访问人数');
        $grid->page_staytime_pv('次均停留时长');
        $grid->entrypage_pv('进入页次数');
        $grid->exitpage_pv('退出页次数');
        $grid->page_share_pv('转发次数');
        $grid->page_share_uv('转发人数');
        $grid->updated_at('更新时间');
        $grid->disableActions();
        $grid->disableRowSelector();
        $grid->disableCreateButton();

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('ref_date', '日期')->datetime(['format' => 'YYYYMMDD']);
        });
        $grid->model()->orderBy('ref_date', 'desc');
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
        $show = new Show(VisitPage::findOrFail($id));

        $show->id('Id');
        $show->gh_id('Gh id');
        $show->ref_date('Ref date');
        $show->page_path('Page path');
        $show->page_visit_pv('Page visit pv');
        $show->page_visit_uv('Page visit uv');
        $show->page_staytime_pv('Page staytime pv');
        $show->entrypage_pv('Entrypage pv');
        $show->exitpage_pv('Exitpage pv');
        $show->page_share_pv('Page share pv');
        $show->page_share_uv('Page share uv');
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
        $form = new Form(new VisitPage);

        $form->text('gh_id', 'Gh id');
        $form->text('ref_date', 'Ref date');
        $form->text('page_path', 'Page path');
        $form->number('page_visit_pv', 'Page visit pv');
        $form->number('page_visit_uv', 'Page visit uv');
        $form->decimal('page_staytime_pv', 'Page staytime pv');
        $form->number('entrypage_pv', 'Entrypage pv');
        $form->number('exitpage_pv', 'Exitpage pv');
        $form->number('page_share_pv', 'Page share pv');
        $form->number('page_share_uv', 'Page share uv');

        return $form;
    }
}
