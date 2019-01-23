<?php
/**
 * Created by PhpStorm.
 * User: jasper
 * Date: 2019/1/14
 * Time: 11:41
 */
namespace App\Admin\Controllers;




use App\Admin\Extensions\tools\editButton;
use App\Admin\Extensions\Tools\modifyTaskMessageStatus;
use App\Admin\Extensions\Tools\WenZiButton;
use App\Http\Controllers\Controller;
use App\Models\Tuwen;
use App\Models\WechatTaskMessage;

use EasyWeChat\Kernel\Messages\Article;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;


class WeChatTaskMessageController extends  Controller
{

    public function index(Request $request,Content $content)
    {
        $content->header('定时消息');
        $content->description('定时群发消息');
        return $content->body($this->grid());
    }


    public function Wenziform()
    {
        $form = new Form(new WechatTaskMessage());
        $form->setAction('/admin/wechat/wenzi');
        $form->datetime('task_time', '定时时间')->format('HH:mm')->required();
        $form->textarea('message_content','消息内容')->required();
        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->setTitle('创建文字消息');
        $form->tools(function (Form\Tools $tools){
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();

            $tools->add('<a href="/admin/wechat/taskMessage" class="btn btn-sm btn-info"><i class="fa fa-list"></i>&nbsp;&nbsp;列表</a>');
        });
        $form->model()->message_type=WechatTaskMessage::TYPE_WENZI;
        $form->model()->admin_user_id=Admin::user()->id;
        return $form;
    }

    public function wenziStore()
    {
        $this->Wenziform()->store();
        return redirect('/admin/wechat/taskMessage');
    }

    public function TuwenForm()
    {
        $form = new Form(new WechatTaskMessage());
        $form->setAction('/admin/wechat/tuwen');
        $form->datetime('task_time', '定时时间')->format('HH:mm')->required();
        $form->hasMany('tuwens', '图文消息', function (Form\NestedForm $form) {
            $form->text('title', '图文标题')->rules('required');
            $form->textarea('desc', '图文描述')->rules('required');
            $form->url('image_url', '图片url')->rules('required');
            $form->url('url', 'url')->rules('required');
        });
        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->setTitle('创建图文消息');
        $form->tools(function (Form\Tools $tools){
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();

            $tools->add('<a href="/admin/wechat/taskMessage" class="btn btn-sm btn-info"><i class="fa fa-list"></i>&nbsp;&nbsp;列表</a>');
        });

        $form->model()->message_type=WechatTaskMessage::TYPE_TUWEN;
        $form->model()->admin_user_id=Admin::user()->id;
        return $form;
    }

    public function tuwenStore()
    {
        $this->TuwenForm()->store();
        return redirect('/admin/wechat/taskMessage');
    }


    public function imageForm()
    {
        $form = new Form(new WechatTaskMessage());
        $form->setAction('/admin/wechat/image');
        $form->datetime('task_time', '定时时间')->format('HH:mm')->required();
        $form->image('image_url','图片')->required()->help('图片大小 2M 以内，尺寸。。。。');
        $form->model()->message_type=WechatTaskMessage::TYPE_IMAGE;
        $form->model()->admin_user_id=Admin::user()->id;
        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->setTitle('创建图片消息');
        $form->tools(function (Form\Tools $tools){
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();

            $tools->add('<a href="/admin/wechat/taskMessage" class="btn btn-sm btn-info"><i class="fa fa-list"></i>&nbsp;&nbsp;列表</a>');
        });



        $form->saved(function(Form $form){
            $image_url=$form->model()->image_url;
            $app = app('wechat.official_account');
            $result = $app->material->uploadImage(public_path().'/upload/'.$image_url);
            if(isset($result['errcode']) && !empty($result['errcode'])){
                $form->destroy($form->model()->id);
                admin_toastr('上传素材失败', 'error');
                return redirect('/admin/wechat/taskMessage');
            }
            $form->model()->media_id=json_encode($result);
            DB::table('wechat_task_messages')->where('id',$form->model()->id)->update([
                'media_id'=>json_encode($result)
            ]);
        });
        return $form;
    }

    public function imageStore()
    {
        $this->imageForm()->store();
        return redirect('/admin/wechat/taskMessage');
    }



    public function grid()
    {
        $grid = new Grid(new WechatTaskMessage);
        $grid->column('admin_user_id','创建者')->display(function($admin_user_id){
            return Administrator::where('id',$admin_user_id)->value('name');
        });
        $grid->id('ID')->sortable();
        $grid->column('message_type','消息类型')->display(function($message_type){
            return WechatTaskMessage::$typeMap[$message_type].'类型';
        });
        $grid->column('content','消息内容')->display(function(){
            if($this->message_type==WechatTaskMessage::TYPE_WENZI){
                return $this->message_content;
            }

            if($this->message_type==WechatTaskMessage::TYPE_IMAGE){
                return '<img src="'.URL::asset('upload/'.$this->image_url).'" width="200px" height="200px">';
            }

            if($this->message_type==WechatTaskMessage::TYPE_TUWEN){
                $tuwens=DB::table('tuwens')->where('task_message_id',$this->id)->get();
                return view('admin.taskMessage.tuwen',compact('tuwens'));
            }
        });
        $grid->column('task_time','推送时间')->display(function($task_time){
             $task_time=explode(':',$task_time);
             return '每天的'.$task_time[0].'时'.$task_time[1].'分';
        });
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');
        $grid->disableExport();
        $grid->disableCreateButton();

        $grid->actions(function($actions){
            $actions->actions=['delete'];
            $url='';
            $edit='编辑';
            if($actions->row->message_type==WechatTaskMessage::TYPE_WENZI){
                $url.="/admin/wechat/wenzi/{$actions->row->id}/edit";
            }
            if($actions->row->message_type==WechatTaskMessage::TYPE_IMAGE){
                $url.="/admin/wechat/image/{$actions->row->id}/edit";
            }

            if($actions->row->message_type==WechatTaskMessage::TYPE_TUWEN){
                $url.="/admin/wechat/tuwen/{$actions->row->id}/edit";
            }

            $actions->append(new editButton($url,$edit));
            if($actions->row->status==0){
                $status='开启';
            }else{
                $status='关闭';
            }

            $actions->append(new modifyTaskMessageStatus($actions->row->id,$status));
        });
        $grid->tools(function ($tools) {
            $url = "/admin/wechat/wenzi";
            $icon = "fa-plus";
            $text = "文字";
            $tools->append(new WenZiButton($url,$icon,$text));

            $url = "/admin/wechat/tuwen";
            $icon = "fa-plus";
            $text = "图文";
            $tools->append(new WenZiButton($url,$icon,$text));

            $url = "/admin/wechat/image";
            $icon = "fa-plus";
            $text = "图片";
            $tools->append(new WenZiButton($url,$icon,$text));
        });
        $grid->model()->orderBy('id','desc');
        return $grid;
    }


    public function createWenzi(Content $content)
    {
        $content->header('新增文字');
        $content->description('添加文字消息');
        return $content->body($this->Wenziform());


    }


    public function createTuwen(Content $content)
    {
        $content->header('新增图文');
        $content->description('添加图文字消息');
        return $content->body($this->TuwenForm());
    }


    public function createImage(Content $content)
    {
        $content->header('新增图片');
        $content->description('添加图片消息');
        return $content->body($this->imageForm());
    }



    public function editWenzi($id,Content $content)
    {
        return $content
            ->header('编辑文字消息')
            ->body($this->Wenziform()->edit($id)->setAction('/admin/wechat/wenzi/'.$id));
    }


    public function updateWenzi(Request $request)
    {
        $this->Wenziform()->update($request->id,$request->all());
        return redirect('/admin/wechat/taskMessage');
    }


    public function editImage($id,Content $content)
    {
        return $content
            ->header('编辑图片消息')
            ->body($this->imageForm()->edit($id)->setAction('/admin/wechat/image/'.$id));
    }

    public function updateImage($id)
    {
        $this->imageForm()->update($id);
        return redirect('/admin/wechat/taskMessage');

    }

    public function editTuwen($id,Content $content)
    {
        return $content
            ->header('编辑图文消息')
            ->body($this->TuwenForm()->edit($id)->setAction('/admin/wechat/tuwen/'.$id));
    }

    public function updateTuwen($id)
    {
        $this->TuwenForm()->update($id);
        return redirect('/admin/wechat/taskMessage');
    }


    public function destroy($id)
    {
        $form=DB::table('wechat_task_messages')->find($id);

        if($form->message_type==WechatTaskMessage::TYPE_TUWEN){
            Tuwen::where('task_message_id',$form->id)->delete();
        }
        $this->Wenziform()->destroy($id);
        return redirect('/admin/wechat/taskMessage');
    }


    public function status($id){
        $form=DB::table('wechat_task_messages')->find($id);
        if($form->status==0){
            $status=1;
        }else{
            $status=0;
        }
        DB::table('wechat_task_messages')->where('id',$id)->update([
            'status'=>$status
        ]);
        return redirect('/admin/wechat/taskMessage');
    }







}