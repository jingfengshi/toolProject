<?php

namespace App\Admin\Controllers;

use App\Models\LandDomain;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class LandDomainsController extends Controller
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
            ->header('落地域名池')
            ->description('落地域名池')
            ->body($this->form())
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

    public function store(Request $request)
    {
        $this->form($request)->store();
        return redirect('/admin/landDomains');
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LandDomain);
        $grid->column('id','ID');
        $grid->column('url','落地域名');
        $grid->column('is_dead','死亡')->display(function($isDead){
            return  $isDead?'<button class="btn btn-sm btn-danger">是</button>':'<button class="btn btn-sm btn-primary">否</button>';
        });
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableActions();
        $grid->filter(function($filter){
            $filter->in('is_dead','死亡')->radio([
                0    =>'未死',
                1    =>'已死',
            ]);
        });
        $grid->model()->orderBy('id','desc');
        $grid->disableRowSelector();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
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
        $show = new Show(LandDomain::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new LandDomain);
        $form->setAction('/admin/landDomains');
        $form->text('url','落地域名')->help('落地域名格式:<b style="color: red">a</b>.qq.com或者<b style="color: red">a</b>.baidu.com 注<b style="color: red">a</b>可以为任意字母组合')->placeholder('落地域名请保证唯一,且主域名唯一');
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();


        return $form;
    }
}
