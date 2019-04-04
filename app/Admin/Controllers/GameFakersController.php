<?php

namespace App\Admin\Controllers;

use App\Models\GameFaker;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class GameFakersController extends Controller
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
            ->header('小游戏伪装页')
            ->description('小游戏伪装页列表')
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
            ->header('创建小游戏伪装页')
            ->description('')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new GameFaker);

        $grid->id('Id');
        $grid->appletId('AppletId');
        $grid->banner_images('轮播图');
        $grid->content_images('小游戏图标');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');
        $grid->actions(function($actions){
            $actions->actions=['edit'];

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
        $show = new Show(GameFaker::findOrFail($id));

        $show->id('Id');
        $show->appletId('AppletId');
        $show->ori_banner_images('Ori banner images');
        $show->ori_content_images('Ori content images');
        $show->banner_images('轮播图');
        $show->content_images('小游戏图标');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new GameFaker);

        $form->text('appletId', 'appId');
        $form->multipleImage('ori_banner_images', '轮播图');
        $form->multipleImage('ori_content_images', '游戏图片集合');
        $form->disableViewCheck();
        $form->disableCreatingCheck();
        $form->disableEditingCheck();

        $form->saving(function(Form $form){
            $form->model()->banner_images='';
            $form->model()->content_images='';
        });

        $form->saved(function(Form $form){
            $banner_images=[];
            foreach ($form->model()->ori_banner_images as $key=> $banner_image){

                $banner_images[]=[
                    'id'=>$key+1,
                    'url'=>env('APP_URL').'/upload/'.$banner_image
                ];
            }

            $content_images=[];
            foreach ($form->model()->ori_content_images as $key=>$content_image){

                $co=Image::make(public_path('upload').DIRECTORY_SEPARATOR.$content_image)->resize('60','60');
                $image_name=date('YmdHis').uniqid().'.png';
                $co->save(public_path('upload').DIRECTORY_SEPARATOR.$image_name);
                $content_images[]=[
                    'id'=>$key+1,
                    'thumb'=>env('APP_URL').'/upload/'.$image_name,
                    'images'=>env('APP_URL').'/upload/'.$content_image,
                ];

            }
            $form->model()->content_images=json_encode($content_images);
            DB::table('game_fakers')->where('id',$form->model()->id)->update([
                'banner_images'=>json_encode($banner_images),
                'content_images'=>json_encode($content_images)
            ]);

        });
        return $form;
    }
}
