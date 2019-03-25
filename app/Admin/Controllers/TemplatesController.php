<?php

namespace App\Admin\Controllers;

use App\Models\Template;
use App\Http\Controllers\Controller;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class TemplatesController extends Controller
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
            ->header('图片处理模板管理')
            ->description('个性化定制处理图片模板')
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


    public function store()
    {

        return $this->form()->store();
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Template);

        $grid->id('ID')->sortable();
        $grid->column('title','模板名称');
        $grid->column('bg_width','背景图片宽度');
        $grid->column('bg_height','背景图片高度');
        $grid->column('code_width','二维码宽度');
        $grid->column('code_height','二维码高度');
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');
        $grid->disableExport();
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
        $show = new Show(Template::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Template);
        $form->setTitle('创建自定义海报模板');
        $form->setAction('/admin/template');
        $form->text('title', '模板名称')->rules('required');

        $form->image('bg_image','背景图片')->attribute(['id'=>'image']);

        $form->html( view('admin.template.jcrop') );
        $form->text('bg_width','背景图片宽度')->default(0)->attribute(['id'=>'b_w']);
        $form->text('bg_height','背景图片高度')->default(0)->attribute(['id'=>'b_h']);
        $states = [
            'on'  => ['value' => 1, 'text' => '确认', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '取消', 'color' => 'danger'],
        ];

        $form->switch('confirm','背景图片宽高确定')->states($states)->attribute(['id'=>'confirm']);
        $form->text('code_start_x','二维码开始x坐标')->default(0)->attribute(['id' => 'x1']);
        $form->text('code_start_y','二维码开始y坐标')->default(0)->attribute(['id' => 'y1']);
        $form->text('code_end_x','二维码结束x坐标')->default(0)->attribute(['id' => 'x2']);
        $form->text('code_end_y','二维码结束y坐标')->default(0)->attribute(['id' => 'y2']);
        $form->text('code_width','二维码宽度')->default(0)->attribute(['id' => 'w']);
        $form->text('code_height','二维码高度')->default(0)->attribute(['id' => 'h']);
        Admin::script($this->jCropScript());
        $form->disableViewCheck();
        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->submitted(function(Form $form){
           $form->ignore(['confirm','bg_image']);
        });
        return $form;
    }

    public function jCropScript()
    {
        return $script=<<<EOT
var demo2 = function() {
        var jcrop_api;

        $('#target').Jcrop({
            onChange:   showCoords,
            onSelect:   showCoords,
            onRelease:  clearCoords
        },function(){
            jcrop_api = this;
        });

        $('#coords').on('change','input',function(e){
            var x1 = $('#x1').val(),
                x2 = $('#x2').val(),
                y1 = $('#y1').val(),
                y2 = $('#y2').val();
            jcrop_api.setSelect([x1,y1,x2,y2]);
        });

        // Simple event handler, called from onChange and onSelect
        // event handlers, as per the Jcrop invocation above
        function showCoords(c)
        {
            $('#x1').val(c.x);
            $('#y1').val(c.y);
            $('#x2').val(c.x2);
            $('#y2').val(c.y2);
            $('#w').val(c.w);
            $('#h').val(c.h);
        };

        function clearCoords()
        {
            $('#coords input').val('');
        };
        
      
        
    }
    
      $('#b_w').on("blur",function(event){
          var b_w=$('#b_w').val();
          if(isNaN(b_w)) $('#b_w').val(0);
          $('#target').css('width',b_w+'px');
       });
       $('#b_h').on("blur",function(event){
          var b_h=$('#b_h').val();
          if(isNaN(b_h)) $('#b_h').val(0);
          $('#target').css('height',b_h+'px');
       });
         $('input[name=confirm]').on('change',function(){
            if( $(this).val()=='off'){
                window.location.href=''
            }else{
                demo2()
            }
        });
        $('#image').on('change',function(){
            var file = $("#image").get(0).files[0];
           
            console.log('height:'+this.height+'----width:'+this.width)
            
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(e) {
                  var image = new Image();
                    console.dir(file)
                  image.onload=function(){
                     console.dir(this.width)
                  }
                  
             }
            reader.onloadend = function () {
                
                $("#target").attr("src", reader.result);
                $("#target").load(function(){
                   $('#b_w').val(this.width)
                   $('#b_h').val(this.height)
                })
            }
            
        });
       
  
EOT;

    }

    public function allTemplates(Request $request)
    {
        $q = $request->get('q');
        if(empty($q)){
            return Template::paginate(null, ['id', 'title as text']);
        }
        return Template::where('title', 'like', "%$q%")->paginate(null, ['id', 'title as text']);
    }
}
