<?php

namespace App\Admin\Controllers;

use App\Events\PostCreated;
use App\Models\Poster;
use App\Http\Controllers\Controller;
use App\Models\Template;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PosterController extends Controller
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
            ->header('海报管理')
            ->description('生成海报')
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
        $grid = new Grid(new Poster);

        $grid->id('ID')->sortable();
        $grid->column('title','海报名称');
        $grid->column('bg_image','背景图片')->display(function($bg_image){
            $url=Storage::disk('qiniu')->url($bg_image);
            return '<img class="bg" src="'.$url.'" height="50px" width="50px"> ';
        });;
        $grid->column('code_image','二维码')->display(function($code_image){
            $url=Storage::disk('qiniu')->url($code_image);
            return '<img src="'.$url.'" height="50px" width="50px">';
        });
        $grid->column('poster_image','合成海报')->display(function($poster_image){
            $url=Storage::disk('qiniu')->url($poster_image);
            return '<img class="bg" src="'.$url.'" height="50px" width="50px"> <button class="poster_button btn-group t btn btn-sm btn-success"  data-id="'.$this->id.'">下载海报</button>';
        });;
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');
        $grid->disableExport();
        $grid->disableActions();
        $grid->model()->orderBy('id','desc');
        Admin::script($this->layerPhote());
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
        $show = new Show(Poster::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Poster);
        $form->setTitle('生成海报【请在务必按照模板底图要求上传海报底图】');
        $form->setAction('/admin/poster');
        $form->text('title', '海报名称')->rules('required');

        $form->image('bg_image','海报底图');
        $form->image('code_image','二维码图片');
        $form->select('template_id','合成模板')->options(function($id){
            $template=Template::find($id);
            if($template){
                return [$template->id=>$template->title];
            }
        })->ajax('/admin/template/allTemplates');
        $form->disableViewCheck();
        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->submitted(function(Form $form){
           $form->model()->poster_image='';
        });

        $form->saved(function(Form $form){
            event(new PostCreated($form->model()));
        });


        return $form;
    }

    public function download(Request $request)
    {
        $post=Poster::find($request->id);
        return Storage::disk('qiniu')->download($post->poster_image);
    }

    public function layerPhote()
    {
        return $script=<<<EOT
        layui.use('layer', function(){
          var layer = layui.layer;
          
        
        });
         $('.bg').on('click',function(){
          var src=$(this).attr('src')
          var img= " <img src="+src+"  >"
          var index=layer.open({
              type: 1,
              title: false,
              closeBtn: 1,
              area:['500px'],
              skin: 'layui-layer-nobg', //没有背景色
              shadeClose: true,
              content:img
            });
            
          layer.iframeAuto(index);
         
         })       
         $('.poster_button').on('click',function(){
            var id =$(this).data('id');
            window.location.href='/admin/poster/download/'+id
         }) 
         
EOT;

    }
}
