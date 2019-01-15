<?php
/**
 * Created by PhpStorm.
 * User: jasper
 * Date: 2019/1/14
 * Time: 11:41
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ShortDomain;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    public function shortDomain(Request $request,Content $content)
    {

          $content->header('短域名管理');
          $content->description('将长域名转为短域名');
          $content->body($this->form($request));
          return $content->body($this->grid());

    }


    public function store(Request $request)
    {
         $this->form($request)->store();
         return redirect('/admin/tools');
    }



    protected function grid()
    {
        $grid = new Grid(new ShortDomain);
        $grid->id('ID')->sortable();
        $grid->ip('IP');
        $grid->origin_url('原始链接');
        $grid->column('short_url')->display(function($short_url){
            return "<input readonly value=".$short_url." id='s_url'>"."<button style='display: inline-block' class='btn-primary copy'>复制</button>";
        });

        $grid->validate_time('过期时间');
        $grid->column('creator.name','创建用户');
        $grid->created_at('创建时间')->sortable();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableActions();

        if(Admin::user()->id===1){
            $grid->model()->orderBy('id','desc');
        }else{
            $grid->model()->where('admin_user_id',Admin::user()->id)->orderBy('id','desc');
        }
        Admin::script("$('.copy').click(function(){
                $(this).prev().select();
                document.execCommand(\"Copy\"); // 执行浏览器复制命令
        })");
        return $grid;
    }


    protected function form(Request $request)
    {
        $form = new Form(new ShortDomain());
        $form->setAction('/admin/tools');
        $form->text('origin_url', '客户原始链接')->rules('required|url');
        $form->datetime('validate_time', '链接失效时间')->format('YYYY-MM-DD HH:mm:ss');
        $form->hidden('ip');
        $form->hidden('short_url');
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->saving(function(Form $form)use($request){
            $domain = 'http://'.$_SERVER['HTTP_HOST'];
            $domain .= '?url='.base64_encode($form->origin_url.'&'.$form->validate_time);
            $source = [2849184197, 202088835, 211160679];
            $key = array_rand($source);
            $url = 'http://api.weibo.com/2/short_url/shorten.json?source='.$source[$key].'&url_long='.$domain;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt ( $ch, CURLOPT_HEADER, 0 );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $res = curl_exec($ch);
            $res = json_decode($res, true);
            $form->short_url=$res['urls'][0]['url_short'];
            $form->ip=$request->getClientIp();
            $form->model()->admin_user_id=Admin::user()->id;
            return $form;
        });
        return $form;
    }
}
