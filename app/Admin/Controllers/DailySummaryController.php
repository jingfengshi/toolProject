<?php

namespace App\Admin\Controllers;

use App\Models\DailySummary;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class DailySummaryController extends Controller
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
            ->header('概况')
            ->description('小程序概况')
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
            ->header('概况')
            ->description('小程序概况')
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
            ->header('编辑')
            ->description('小程序概况')
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
            ->header('创建')
            ->description('小程序概况')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DailySummary);

        $grid->id('Id');
//        $grid->gh_id('Ghid');
        $grid->column('wechat_applet.name', '名字');
        $grid->ref_date('日期');
        $grid->visit_total('累计用户数')->sortable();
        $grid->share_pv('转发次数')->sortable();
        $grid->share_uv('转发人数')->sortable();
//        $grid->created_at('Created at');
//        $grid->updated_at('Updated at');

        $grid->disableActions();
        $grid->disableRowSelector();
        $grid->disableCreateButton();

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->equal('ref_date', '日期')->datetime(['format' => 'YYYYMMDD']);
        });
        $grid->model()->orderBy('visit_total', 'desc');
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
        $show = new Show(DailySummary::findOrFail($id));

        $show->id('Id');
        $show->gh_id('Gh id');
        $show->ref_date('Ref date');
        $show->visit_total('Visit total');
        $show->share_pv('Share pv');
        $show->share_uv('Share uv');
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
        $form = new Form(new DailySummary);

        $form->text('gh_id', 'Gh id');
        $form->text('ref_date', 'Ref date');
        $form->number('visit_total', 'Visit total');
        $form->number('share_pv', 'Share pv');
        $form->number('share_uv', 'Share uv');

        return $form;
    }
}
