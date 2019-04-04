<?php

namespace App\Admin\Controllers;

use App\Models\UserPortraitAgesUV;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;

class UserPortraitAgesUVController extends Controller
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
            ->description('description')
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
        $grid = new Grid(new UserPortraitAgesUV);

        $grid->id('Id');
        $grid->id('Id');
        $grid->gh_id('Ghid');
        $grid->column('wechat_applet.name', '名字');
        $grid->ref_date('日期');
        $grid->unknown('未知');
        $grid->under17('17岁以下');
        $grid->age18_24('18-24岁');
        $grid->age25_29('25-29岁');
        $grid->age30_39('30-39岁');
        $grid->age40_49('40-49岁');
        $grid->over50('50岁以上');
//        $grid->created_at('Created at');
        $grid->updated_at('更新时间');

        $grid->disableActions();
        $grid->disableRowSelector();
        $grid->disableCreateButton();

        $grid->header(function ($query) {

//            $gender = $query->select(DB::raw('SUM(man) as m, wechat_user_portrait_gender'))
//                ->groupBy('gender')->get()->pluck('count', 'gender')->toArray();

            $ages = $query->first(
                array(
                    \DB::raw('SUM(unknown) as unknown'),
                    \DB::raw('SUM(under17) as under17'),
                    \DB::raw('SUM(age18_24) as age18_24'),
                    \DB::raw('SUM(age25_29) as age25_29'),
                    \DB::raw('SUM(age30_39) as age30_39'),
                    \DB::raw('SUM(age40_49) as age40_49'),
                    \DB::raw('SUM(over50) as over50')
                )
            )->toArray();

            $doughnut = view('admin.chart.ages', compact('ages'));

            return new Box('年龄分布', $doughnut);
        });

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->equal('ref_date', '日期')->datetime(['format' => 'YYYYMMDD']);
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
        $show = new Show(UserPortraitAgesUV::findOrFail($id));

        $show->id('Id');
        $show->gh_id('Gh id');
        $show->ref_date('Ref date');
        $show->unknown('Unknown');
        $show->under17('Under17');
        $show->age18_24('Age18 24');
        $show->age25_29('Age25 29');
        $show->age30_39('Age30 39');
        $show->age40_49('Age40 49');
        $show->over50('Over50');
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
        $form = new Form(new UserPortraitAgesUV);

        $form->text('gh_id', 'Gh id');
        $form->text('ref_date', 'Ref date');
        $form->number('unknown', 'Unknown');
        $form->number('under17', 'Under17');
        $form->number('age18_24', 'Age18 24');
        $form->number('age25_29', 'Age25 29');
        $form->number('age30_39', 'Age30 39');
        $form->number('age40_49', 'Age40 49');
        $form->number('over50', 'Over50');

        return $form;
    }
}
