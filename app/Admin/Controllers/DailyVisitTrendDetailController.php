<?php

namespace App\Admin\Controllers;

use App\Models\DailyVisitTrend;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class DailyVisitTrendDetailController extends Controller
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
        $name = Input::get('name');
        return $content
            ->header($name)
            ->description($name)
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
        $grid = new Grid(new DailyVisitTrend);
        $gh_id = Input::get('gh_id');
        $grid->model()->select()->where('gh_id', $gh_id);
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
        $grid->header(function ($query) {
            $data = $query->select(DB::raw('visit_uv_new, ref_date'))
                ->orderBy('ref_date')->limit(10)->get()->pluck('visit_uv_new', 'ref_date')->toArray();

            $doughnut = view('admin.chart.dailyvisittrenddetail', compact('data'));

            return new Box('新用户数', $doughnut);
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
        $form->decimal('stay_time_uv', 'Stay time uv')->default(0.00);
        $form->decimal('stay_time_session', 'Stay time session')->default(0.00);
        $form->decimal('visit_depth', 'Visit depth')->default(0.00);

        return $form;
    }
}
