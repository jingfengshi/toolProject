<?php

namespace App\Admin\Controllers;

use App\Handlers\FangFengHandler;
use App\Models\LargeShortUrl;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;


class LargeShortUrlsController extends Controller
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
            ->header('海量防封短域名生成器')
            ->description('短域名生成')
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
        $grid = new Grid(new LargeShortUrl);

        $grid->id('Id');
        $grid->url('原始域名');
       // $grid->to_url('防封域名');
        $grid->short_url('防封短域名')->display(function($short_url){
            return "<textarea>$short_url </textarea>";
        });
        $grid->created_at('创建时间');
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableActions();
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
        $show = new Show(LargeShortUrl::findOrFail($id));

        $show->id('Id');
        $show->url('Url');
        $show->to_url('To url');
        $show->short_url('Short url');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    public function store(Request $request)
    {
        $this->form($request)->store();
        return redirect('/admin/largeShortUrl');
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new LargeShortUrl);
        $form->setAction('/admin/largeShortUrl');
        $form->url('url', '原始域名')->rules('required|url');
        $form->number('number', '生成数量')->rules('required')->min(1)->max(20);
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->ignore('number');
        $form->saving(function(Form $form){
            $form->model()->to_url='';
            $short_url=[];
            $times=request('number');
            for($i=0;$i<$times;$i++){
                $res=FangFengHandler::getShortUrl(request('url'));
                if($res){
                    $short_url[]=$res['tourl'];
                }
            }
            $form->model()->short_url=implode(',',$short_url);
            return $form;
        });
        return $form;
    }
}
