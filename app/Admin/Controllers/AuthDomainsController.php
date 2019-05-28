<?php

namespace App\Admin\Controllers;

use App\Models\AuthDomain;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class AuthDomainsController extends Controller
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
            ->header('授权域名池')
            ->description('授权域名池')
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

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AuthDomain);
        $grid->column('id','ID');
        $grid->column('url','授权域名');
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
        $show = new Show(AuthDomain::findOrFail($id));



        return $show;
    }

    public function store(Request $request)
    {
        $this->form($request)->store();
        return redirect('/admin/authDomains');
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AuthDomain);
        $form->setAction('/admin/authDomains');
        $form->text('url','授权域名')->help('授权域名格式:qq.com或者baidu.com');
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();


        return $form;
    }
}
