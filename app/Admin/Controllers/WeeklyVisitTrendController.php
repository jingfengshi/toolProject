<?php

namespace App\Admin\Controllers;

use App\Models\WeeklyVisitTrend;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeeklyVisitTrendController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Request $request
     * @param Content $content
     * @return Content
     */
    public function index(Request $request, Content $content)
    {
        return $content
            ->header('Index')
            ->description('获取用户访问小程序数据周趋势')
            ->body($this->grid($request));
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
            ->description('获取用户访问小程序数据周趋势')
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
            ->description('获取用户访问小程序数据周趋势')
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
            ->description('获取用户访问小程序数据周趋势')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @param $request
     * @return Grid
     */
    protected function grid($request)
    {
        $grid = new Grid(new WeeklyVisitTrend);
        if (strpos($request->getQueryString(),'created_at') === false) {
            $weekDay = date('w');
            $lastSunday = date('Ymd', strtotime('-1 sunday', time()));
            if ($weekDay == 1) {
                $lastMonday = date('Ymd', strtotime('-1 monday', time()));
            } else {
                $lastMonday = date('Ymd', strtotime('-2 monday', time()));
            }
            $dateStr = $lastMonday . '-' . $lastSunday;
            Log::info($dateStr);
            $grid->model()->select()->where('ref_date', $dateStr);
        }
        $grid->id('Id');
        $grid->column('wechat_applet.name', '名字');
        $grid->ref_date('日期');
        $grid->visit_uv_new('新用户数')->sortable();
        $grid->session_cnt('打开次数')->sortable();
        $grid->visit_pv('访问次数')->sortable();
        $grid->visit_uv('访问人数')->sortable();
        $grid->stay_time_uv('人均停留时长(秒)')->sortable();
        $grid->stay_time_session('次均停留时长(秒)')->sortable();
        $grid->visit_depth('平均访问深度');
        $grid->updated_at('更新时间');
        $grid->disableActions();
        $grid->disableRowSelector();
        $grid->disableCreateButton();
//        $grid->model()->orderBy('ref_date', 'desc');
        $grid->model()->orderBy('visit_uv_new', 'desc');
        $grid->model()->orderBy('id', 'desc');
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->between('created_at', '时间')->datetime(['format' => 'YYYY-MM-DD']);
//            $filter->column(1/2, function ($filter) {
//                $filter->between('created_at', '时间')->datetime(['format' => 'YYYYMMDD']);
////                $filter->where(function ($query) {
////                    $query->where('ref_date', 'like', "%{$this->input}%");
////                }, '周一-周日');
//            });
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
        $show = new Show(WeeklyVisitTrend::findOrFail($id));

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
        $form = new Form(new WeeklyVisitTrend);

        $form->text('gh_id', 'Gh id');
        $form->text('ref_date', 'Ref date');
        $form->number('session_cnt', 'Session cnt');
        $form->number('visit_pv', 'Visit pv');
        $form->number('visit_uv', 'Visit uv');
        $form->number('visit_uv_new', 'Visit uv new');
        $form->decimal('stay_time_uv', 'Stay time uv')->default(0.00);
        $form->decimal('stay_time_session', 'Stay time session')->default(0.00);
        $form->decimal('visit_depth', 'Visit depth')->default(0.00);

        return $form;
    }
}
