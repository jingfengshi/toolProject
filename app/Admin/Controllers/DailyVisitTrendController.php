<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DailyVisitTrend;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

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
        $ref_date = Request::get('ref_date', 0);
        if (!$ref_date) {
            $dateStr = trim(date("Ymd", strtotime("-1 day")));
            $grid->model()->select()->where('ref_date', $dateStr);
        }
        $grid->id('Id');
//        $grid->gh_id('Ghid');
        $grid->column('wechat_applet.name', '名字');
        $grid->ref_date('日期');
        $grid->visit_uv_new('新用户数')->sortable();
        $grid->session_cnt('打开次数')->sortable();
        $grid->visit_pv('访问次数')->sortable();
        $grid->visit_uv('访问人数')->sortable();
        $grid->stay_time_uv('人均停留时长(秒)')->sortable();
        $grid->stay_time_session('次均停留时长(秒)')->sortable();
        $grid->visit_depth('平均访问深度');
        $grid->column('进入次数')->display(function () {
            $data = DB::table('daily_wechat_mini_visit')->where(['gh_id'=>$this->gh_id, 'ref_date'=>$this->ref_date])->select(['enter_times'])->first();
            if ($data) {
                return $data->enter_times;
            } else {
                return 0;
            }
        });
        $grid->column('回复次数')->display(function () {
            $data = DB::table('daily_wechat_mini_visit')->where(['gh_id'=>$this->gh_id, 'ref_date'=>$this->ref_date])->select(['reply_times'])->first();
            if ($data) {
                return $data->reply_times;
            } else {
                return 0;
            }
        });
        $grid->updated_at('更新时间');
        $grid->disableActions();
        $grid->disableRowSelector();
        $grid->disableCreateButton();

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('ref_date', '日期')->datetime(['format' => 'YYYYMMDD']);
        });
        $grid->header(function ($query) {
            $visit_uv_new = $query->sum('visit_uv_new');
            $session_cnt = $query->sum('session_cnt');
            $visit_pv = $query->sum('visit_pv');
            $visit_uv = $query->sum('visit_uv');

            return "<div style='padding: 10px;color:red;font-weight:bold;'>总新用户数 ： $visit_uv_new<span style='margin-left: 20px;'></span>总打开次数：$session_cnt<span style='margin-left: 20px;'></span>总访问次数：$visit_pv<span style='margin-left: 20px;'></span>总访问人数：$visit_uv</div>";
        });
        $grid->model()->orderBy('visit_uv_new', 'desc');
        $grid->model()->orderBy('id', 'desc');
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
