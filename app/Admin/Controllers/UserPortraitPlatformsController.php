<?php

namespace App\Admin\Controllers;

use App\Models\UserPortraitPlatforms;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Log;

class UserPortraitPlatformsController extends Controller
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
            ->description('平台统计')
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
        $grid = new Grid(new UserPortraitPlatforms);

        $grid->id('Id');
        $grid->gh_id('Ghid');
        $grid->column('wechat_applet.name', '名字');
        $grid->ref_date('日期');
        $grid->iphone('iPhone');
        $grid->android('Android');
        $grid->other('其他');
//        $grid->created_at('Created at');
        $grid->updated_at('更新时间');

        $grid->disableActions();
        $grid->disableRowSelector();
        $grid->disableCreateButton();

        $grid->header(function ($query) {

//            $gender = $query->select(DB::raw('SUM(man) as m, wechat_user_portrait_gender'))
//                ->groupBy('gender')->get()->pluck('count', 'gender')->toArray();

            $platforms = $query->first(
                array(
                    \DB::raw('SUM(android) as android'),
                    \DB::raw('SUM(iphone) as iphone'),
                    \DB::raw('SUM(other) as other')
                )
            )->toArray();

            $doughnut = view('admin.chart.platforms', compact('platforms'));

            return new Box('平台分布', $doughnut);
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
        $show = new Show(UserPortraitPlatforms::findOrFail($id));

        $show->id('Id');
        $show->gh_id('Gh id');
        $show->ref_date('Ref date');
        $show->iphone('Iphone');
        $show->android('Android');
        $show->other('Other');
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
        $form = new Form(new UserPortraitPlatforms);

        $form->text('gh_id', 'Gh id');
        $form->text('ref_date', 'Ref date');
        $form->number('iphone', 'Iphone');
        $form->number('android', 'Android');
        $form->number('other', 'Other');

        return $form;
    }
}
