<?php
namespace App\Services;


use App\Models\WechatTaskMessage;
use Carbon\Carbon;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendWechatMessage
{

    public function getCurrentTask()
    {
        $time=Carbon::now()->second(0)->format('H:i:s');

        return WechatTaskMessage::where([
            'status'=>1
            ])->whereTime('task_time','=',$time)->get();
    }

    public function sendMessage()
    {
        $app = app('wechat.official_account');
        $tasks=$this->getCurrentTask();
        if(!$tasks->isEmpty()){
            foreach ($tasks as $task){
                if($task->message_type==WechatTaskMessage::TYPE_WENZI){
                    $message=new Text($task->message_content);
                    $openids=$this->getUserListOpenIds();
                    foreach ($openids as $openid){
                        $app->customer_service->message($message)->to($openid)->send();
                    }
                }


                if($task->message_type==WechatTaskMessage::TYPE_TUWEN){
                    $tuwen=DB::table('tuwens')->where('task_message_id',$task->id)->first();
                    $item=new NewsItem([
                        'title'       =>$tuwen->title,
                        'description' => $tuwen->desc,
                        'url'         => $tuwen->url,
                        'image'       => $tuwen->image_url,
                    ]);
                    $news = new News([$item]);
                    $openids=$this->getUserListOpenIds();
                    foreach ($openids as $openid){
                        $app->customer_service->message($news)->to($openid)->send();
                    }
                }
                if($task->message_type==WechatTaskMessage::TYPE_IMAGE){
                    $media_id=json_decode($task->media_id,true);

                    $image = new Image($media_id['media_id']);
                    $openids=$this->getUserListOpenIds();
                    foreach ($openids as $openid){
                        $app->customer_service->message($image)->to($openid)->send();
                    }
                }
            }
        }


    }

    public function getUserListOpenIds()
    {
        $app = app('wechat.official_account');
        $user = $users = $app->user->list();
        return $user['data']['openid'];
    }







}