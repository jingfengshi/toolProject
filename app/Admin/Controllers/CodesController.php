<?php

namespace App\Admin\Controllers;

use App\Events\CodeCreate;
use App\Handlers\DomainsToCode;
use App\Handlers\zipFile;
use App\Models\Code;
use App\Http\Controllers\Controller;
use App\Models\CodeItem;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;

use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

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
        $grid->column('download','打包下载二维码')->display(function(){
            return '<button data-url="'.$this->id.'" class="poster-package  btn-group t btn btn-sm btn-success"  data-id="'.$this->id.'">打包下载</button>';
        });
        Admin::script($this->codeJs());
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

    public function downloadPackage(Request $request){
        error_reporting(0);
        $post=Code::find($request->id);
        $dfile = tempnam(public_path('tmp'), 'tmp');//产生一个临时文件，用于缓存下载文件
        $zip = new zipfile();
//----------------------
        $filename = $request->id.'qrcodes.zip'; //下载的默认文件名

//以下是需要下载的图片数组信息，将需要下载的图片信息转化为类似即可
        $image =[];
        $code_images=explode(',',$post->code_images);
        foreach ($code_images as $index=> $item) {
            \Log::error(Storage::disk('qiniu')->url($item));
            \Log::error($item);
            $image[]=[
                'image_src'=>Storage::disk('qiniu')->url($item),
                'image_name'=>$request->id.'_qrcodes_'.($index+1).'.png',
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

    public function codeJs()
    {
        return $script=<<<EOT
         $('.poster-package').on('click',function(){
           var id=$(this).data('id')
            window.location.href='/admin/codes/download_package/'+id
         })         
EOT;

    }
}
