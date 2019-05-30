<?php

namespace App\Admin\Controllers;

use App\Models\SpreadDomain;
use App\Models\unBlockDomain;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnBlockDomainsController extends Controller
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
            ->header('新防封域名管理')
            ->description('防封域名管理')
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
        $grid = new Grid(new unBlockDomain);

        $grid->id('Id');
        $grid->origin_url('原始域名');
        $grid->unblock_url('防封域名')->display(function($short_url){
                $short_url=json_decode($short_url,true);
                $url='';
                foreach ($short_url as $item){
                    $url.=$item.',';
                }
                $url =rtrim($url,',');
            return "<textarea>$url</textarea>";
        });
        $grid->created_at('创建时间');
        $grid->disableCreateButton();
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
        $show = new Show(unBlockDomain::findOrFail($id));



        return $show;
    }

    public function store(Request $request)
    {
        $this->form($request)->store();
        return redirect('/admin/unBlockDomains');
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new unBlockDomain);
        $form->setAction('/admin/unBlockDomains');
        $form->url('origin_url', '原始域名')->rules('required|url');
        $form->number('number', '生成防封域名数量')->rules('required')->min(1)->max(10);

        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->ignore('number');
        $form->saving(function($form){
            $spread_urls =SpreadDomain::where('is_dead',false)->orderBy(DB::raw('RAND()'))->pluck('url')->toArray();
            $times = request('number');
            $hit_urls=[];
            for ($i=0;$i<$times;$i++){
                $hit_urls[]=$spread_urls[array_rand($spread_urls)];
            }
            $orgin_url =base64_encode($form->origin_url);
            $prefix =$this->randomkeys(8);
            $unBlock_url = [];
            foreach ($hit_urls as $hit_url){
                $unBlock_url[] ='http://'.$prefix.'.'.$hit_url.'/spread/'.$orgin_url;
            }
            $form->model()->user_id =Admin::user()->id;
            $form->model()->unblock_url = json_encode($unBlock_url);

        });

        return $form;
    }


    protected function randomkeys($length)
    {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,35)};    //生成php随机数
        }
        return $key;
    }
}
