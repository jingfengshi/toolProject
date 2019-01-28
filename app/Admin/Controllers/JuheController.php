<?php
/**
 * Created by PhpStorm.
 * User: jasper
 * Date: 2019/1/14
 * Time: 11:41
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Juhe;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class JuheController extends Controller
{
     public function index(Request $request,Content $content)
     {
         $content->header('聚合页管理');
         $content->description('获取聚合页链接');
         return $content->body($this->grid());
     }


     public function create(Content $content)
     {
         return $content
             ->header('创建聚合页')
             ->body($this->form());
     }

     public function store()
     {

         return $this->form()->store();
     }


     public function grid()
     {
         $grid = new Grid(new Juhe());
         $grid->id('ID')->sortable();
         $grid->column('type','类型')->display(function($type){
             return Juhe::$typeMap[$type];
         });
         $grid->url('跳转链接')->display(function($url){
             return "<input readonly value=".$url.">";
         });
         $grid->column('site_url','页面url')->display(function($site_url){
             return "<input readonly value=".$site_url." id='s_url'>"."<button style='display: inline-block' class='btn-primary copy'>复制</button>";
         });
         $grid->created_at('创建时间')->sortable();
         $grid->column('admin_user_id','创建者')->display(function($admin_user_id){
             return Administrator::where('id',$admin_user_id)->value('name');
         });
         $grid->disableExport();
         $grid->disableActions();
         Admin::script("$('.copy').click(function(){
                $(this).prev().select();
                document.execCommand(\"Copy\"); // 执行浏览器复制命令
        })");
         return $grid;
     }

    protected function form()
    {
        $form = new Form(new Juhe());
        $form->select('type','类型')->options(['ad' => '单图广告', 'juhe' => '聚合页'])->required();
        $form->url('url','链接地址')->required();
        $form->multipleImage('images', '配图')->required()->move('images/'.date("Ym/d", time()));
        $form->model()->admin_user_id=Admin::user()->id;
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->saved(function(Form $form){
            $images=$form->model()->images;
            $url=$form->url;
            $html=view('admin.wechatTools.tools',compact('images','url'))->render();
            $name =date("Ym/d", time()).'/'. md5($url.time()).'.html';
            Storage::disk('qiniu')->put($name,$html);
            $local_url=Storage::disk('qiniu')->url($name);
            DB::table('juhes')->where('id',$form->model()->id)->update(['site_url'=>$local_url]);
        });
        return $form;
    }


    public function destroy($id)
    {
        $this->form()->destroy($id);
        return redirect('/admin/wechat/taskMessage');
    }





}