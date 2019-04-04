<?php

namespace App\Admin\Controllers;

use App\Models\UserPortraitGenderUV;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Log;

class UserPortraitGenderUVController extends Controller
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
        $grid = new Grid(new UserPortraitGenderUV);

        $grid->id('Id');
        $grid->gh_id('Ghid');
        $grid->column('wechat_applet.name', '名字');
        $grid->ref_date('日期');
        $grid->male('男');
        $grid->female('女');
        $grid->unknown('未知');
//        $grid->created_at('Created at');
        $grid->updated_at('更新时间');
        $grid->disableActions();
        $grid->disableRowSelector();
        $grid->disableCreateButton();

        $grid->header(function ($query) {

//            $gender = $query->select(DB::raw('SUM(man) as m, wechat_user_portrait_gender'))
//                ->groupBy('gender')->get()->pluck('count', 'gender')->toArray();

            $gender = $query->first(
                array(
                    \DB::raw('SUM(male) as m'),
                    \DB::raw('SUM(female) as f'),
                    \DB::raw('SUM(unknown) as u')
                )
            )->toArray();
            Log::info($gender);

            $doughnut = view('admin.chart.gender', compact('gender'));

            return new Box('性别比例', $doughnut);
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
        $show = new Show(UserPortraitGenderUV::findOrFail($id));

        $show->id('Id');
        $show->gh_id('Gh id');
        $show->ref_date('Ref date');
        $show->male('Male');
        $show->female('Female');
        $show->unknown('Unknown');
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
        $form = new Form(new UserPortraitGenderUV);

        $form->text('gh_id', 'Gh id');
        $form->text('ref_date', 'Ref date');
        $form->number('male', 'Male');
        $form->number('female', 'Female');
        $form->number('unknown', 'Unknown');

        return $form;
    }
}
