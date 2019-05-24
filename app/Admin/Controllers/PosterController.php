<?php

namespace App\Admin\Controllers;

use App\Events\PostCreated;
use App\Handlers\zipFile;
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
use Intervention\Image\Facades\Image;


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

            return '<img class="bg" src="/storage/'.$bg_image.'" height="50px" width="50px"> ';
        });;
        $grid->column('code_image','二维码')->display(function($code_image){
           // $url=Storage::disk('qiniu')->url($code_image);
            \Log::error($code_image);
            $div='';
            $code_images=collect($code_image);

            foreach ($code_images->chunk(3) as $images){

                foreach ($images as $image){
                    $div.='<img src="/storage/'.$image.'" height="50px" width="50px">  ';
                }
                $div.='<hr>';
            }
            return $div;
        });
        $grid->column('poster_image','合成海报')->display(function($poster_image){
            $poster_images=collect($poster_image);
            $div='';
            foreach ($poster_images->chunk(3) as $images){

                foreach ($images as $image){
                    $url=Storage::disk('qiniu')->url($image);
                   $div.= '<img class="bg" src="'.$url.'" height="50px" width="50px"> <button class="poster_button btn-group t btn btn-sm btn-success"  data-url="'.$image.'">下载</button>';
                }
                $div.='<hr>';

            }
            return $div;

        });
        $grid->column('download','打包下载海报')->display(function(){
            return '<button data-url="'.$this->id.'" class="poster-package  btn-group t btn btn-sm btn-success"  data-id="'.$this->id.'">打包下载</button>';
        });
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
        $form->multipleImage('code_image','二维码图片');
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

        $form->saving(function(Form $form){
            $template=Template::find($form->template_id);
            $bg=Image::make($form->bg_image);
            $poster_image=[];
            //获取背景图
            foreach ($form->code_image as $item){
                $co=Image::make($item)->resize($template->code_width,$template->code_height);
                $bg->insert($co,'top-left',$template->code_start_x,$template->code_start_y);
                $image_name=date('YmdHis').uniqid().'.png';
                $bg->save(storage_path('app/public/images').DIRECTORY_SEPARATOR.$image_name);
                Storage::disk('qiniu')->put($image_name,file_get_contents(storage_path('app/public/images').DIRECTORY_SEPARATOR.$image_name));
                $poster_image[]=$image_name;
            }
            $form->model()->poster_image=json_encode($poster_image);
          //
        });




        return $form;
    }

    public function download(Request $request)
    {
        return Storage::disk('qiniu')->download($request->url);
    }


    public function downloadPackage(Request $request){
        error_reporting(0);
        $post=Poster::find($request->id);
        $dfile = tempnam(public_path('tmp'), 'tmp');//产生一个临时文件，用于缓存下载文件
        $zip = new zipfile();
//----------------------
        $filename = 'image.zip'; //下载的默认文件名

//以下是需要下载的图片数组信息，将需要下载的图片信息转化为类似即可
        $image =[];
        foreach ($post->poster_image as $item) {
            $image[]=[
              'image_src'=>Storage::disk('qiniu')->url($item),
              'image_name'=>$item,
            ];
        }

        foreach($image as $v){
            $zip->add_file(file_get_contents($v['image_src']), $v['image_name']);
            // 添加打包的图片，第一个参数是图片内容，第二个参数是压缩包里面的显示的名称, 可包含路径
            // 或是想打包整个目录 用 $zip->add_path($image_path);
        }
//----------------------
        $zip->output($dfile);

// 下载文件
        ob_clean();
        header('Pragma: public');
        header('Last-Modified:'.gmdate('D, d M Y H:i:s') . 'GMT');
        header('Cache-Control:no-store, no-cache, must-revalidate');
        header('Cache-Control:pre-check=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding:binary');
        header('Content-Encoding:none');
        header('Content-type:multipart/form-data');
        header('Content-Disposition:attachment; filename="'.$filename.'"'); //设置下载的默认文件名
        header('Content-length:'. filesize($dfile));
        $fp = fopen($dfile, 'r');
        while(connection_status() == 0 && $buf = @fread($fp, 8192)){
            echo $buf;
        }
        fclose($fp);
        @unlink($dfile);
        @flush();
        @ob_flush();
        exit();

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
            var url =$(this).data('url');
            window.location.href='/admin/poster/download/?url='+url
         }) 
         
         
         $('.poster-package').on('click',function(){
           var id=$(this).data('id')
            window.location.href='/admin/poster/download_package/'+id
         })
         
EOT;

    }
}
