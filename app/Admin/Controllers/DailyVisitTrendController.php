<?php

namespace App\Admin\Controllers;

use App\Models\DailyVisitTrend;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class DailyVisitTrendController extends Controller
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
            ->header('日趋势')
            ->description('小程序数据日趋势')
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
            ->description('小程序数据日趋势')
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
            ->description('小程序数据日趋势')
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
            ->description('小程序数据日趋势')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DailyVisitTrend);

        $grid->id('Id');
//        $grid->gh_id('Ghid');
        $grid->column('wechat_applet.name', '名字');
        $grid->ref_date('日期');
        $grid->session_cnt('打开次数')->sortable();
        $grid->visit_pv('访问次数')->sortable();
        $grid->visit_uv('访问人数')->sortable();
        $grid->visit_uv_new('新用户数')->sortable();
        $grid->stay_time_uv('人均停留时长(秒)')->sortable();
        $grid->stay_time_session('次均停留时长(秒)')->sortable();
        $grid->visit_depth('平均访问深度');
//        $grid->created_at('Created at');
        $grid->updated_at('更新时间');
        $grid->disableActions();
        $grid->disableRowSelector();
        $grid->disableCreateButton();

//        $grid->filter(function($filter){
//            $filter->disableIdFilter();
//            $filter->equal('ref_date')->datetime(['format' => 'YYYYMM']);
//        });

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
        $show = new Show(DailyVisitTrend::findOrFail($id));

        $show->id('Id');
        $show->gh_id('Gh id');
        $show->ref_date('Ref date');
        $show->session_cnt('Session cnt');
        $show->visit_pv('Visit pv');
        $show->visit_uv('Visit uv');
        $show->visit_uv_new('Visit uv new');
        $show->stay_time_uv('Stay time uv');
        $show->stay_time_session('Stay time session');
        $show->visit_depth('Visit depth');
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
        $form = new Form(new DailyVisitTrend);

        $form->text('gh_id', 'Gh id');
        $form->text('ref_date', 'Ref date');
        $form->number('session_cnt', 'Session cnt');
        $form->number('visit_pv', 'Visit pv');
        $form->number('visit_uv', 'Visit uv');
        $form->number('visit_uv_new', 'Visit uv new');
        $form->decimal('stay_time_uv', 'Stay time uv');
        $form->decimal('stay_time_session', 'Stay time session');
        $form->decimal('visit_depth', 'Visit depth');

        return $form;
    }
}
