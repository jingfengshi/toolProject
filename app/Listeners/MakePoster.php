<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Models\Template;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class MakePoster implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PostCreated  $event
     * @return void
     */
    public function handle(PostCreated $event)
    {
        $poster = $event->poster;
        //获取图片合成的模型
        $template=Template::find($poster->template_id);
        //获取背景图
        $bg=Image::make(public_path('upload').DIRECTORY_SEPARATOR.$poster->bg_image);
        $co=Image::make(public_path('upload').DIRECTORY_SEPARATOR.$poster->code_image)->resize($template->code_width,$template->code_height);
        $bg->insert($co,'top-left',$template->code_start_x,$template->code_start_y);
        $image_name=date('YmdHis').uniqid().'.png';
        $bg->save(public_path('upload').DIRECTORY_SEPARATOR.$image_name);
        Storage::disk('qiniu')->put('/posters/'.$image_name,file_get_contents(public_path('upload').DIRECTORY_SEPARATOR.$image_name));
        Storage::disk('qiniu')->put('/posters/'.$poster->bg_image,Storage::disk('admin')->get($poster->bg_image));
        Storage::disk('qiniu')->put('/posters/'.$poster->code_image,Storage::disk('admin')->get($poster->code_image));
        @unlink(public_path('upload').DIRECTORY_SEPARATOR.$image_name);
        Storage::disk('admin')->delete($poster->code_image);
        Storage::disk('admin')->delete($poster->bg_image);
        DB::table('posters')->where(['id'=>$poster->id])->update([
            'poster_image'=>'/posters/'.$image_name,
            'bg_image'=>'/posters/'.$poster->bg_image,
            'code_image'=>'/posters/'.$poster->code_image
        ]);
    }
}
