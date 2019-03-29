<?php

namespace App\Admin\Controllers;

use App\Events\CodeCreate;
use App\Handlers\DomainsToCode;
use App\Models\Code;
use App\Http\Controllers\Controller;
use App\Models\CodeItem;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;

use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\Config;

class CodesController extends Controller
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
            ->header('域名生成二维码')
            ->description('将域名批量生成二维码')
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
        $grid = new Grid(new Code);
        $grid->id('ID')->sortable()->expand(function($model){
            $code_items=new CodeItem();
            $items=$code_items->where('code_id',$model->id)->get()->map(function($codeItem){
                return collect($codeItem)->map(function($item,$key){

                    if($key=='code_image'){
                        return Config::get('filesystems.disks.qiniu.domains.default').$item;
                    }else{
                        return $item;
                    }

                })->only(['id','url','code_image','created_at']);
            });
            return new Table(['ID', '域名', '二维码链接','创建时间'], $items->toArray());
        });
        $grid->urls()->display(function($url){
            return "<textarea>$url </textarea>";
        });;
        $grid->column('code_images','二维码包')->display(function($code_images){
            return "<textarea>$code_images </textarea>";
        });
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');
        $grid->disableExport();
        $grid->disableActions();
        $grid->model()->orderBy('id','desc');


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
        $show = new Show(Code::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Code);
        $form->setTitle('批量生成二维码');
        $form->setAction('/admin/code');
        $form->textarea('urls', '域名池')->rules('required');
        $form->saving(function(Form $form){
            $form->model()->code_images='';
        });
        $form->saved(function(Form $form){
            event(new CodeCreate($form->model()));
        });
        $form->disableViewCheck();
        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        return $form;
    }
}
