<?php

namespace App\Admin\Controllers;

use App\Models\OpenId;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OpenIdsController extends Controller
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
            ->header('用户openId管理')
            ->description('用户openId管理')
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
        $grid = new Grid(new OpenId);

        $grid->column('id');
        $grid->column('ip');
        $grid->column('area','地区');
        $grid->column('device','设备');
        $grid->column('open_id','OPEN_ID');


        $grid->actions(function($actions){
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            $row = $actions->row;
            if($row->block){
                $actions->append('<button class="btn btn-sm btn-danger block" data-id="'.$row->id.'">拉白</button>');
            }else{
                $actions->append('<block class="btn btn-sm btn-primary block" data-id="'.$row->id.'">拉黑</block>');
            }

        });
        Admin::script($this->blockIp());

        $grid->disableCreateButton();

        $grid->disableExport();

        return $grid;

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
        $show = new Show(OpenId::findOrFail($id));

        $show->id('Id');
        $show->ip('Ip');
        $show->open_id('Open id');
        $show->area('Area');
        $show->device('Device');
        $show->block('Block');
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
        $form = new Form(new OpenId);

        $form->ip('ip', 'Ip');
        $form->text('open_id', 'Open id');
        $form->text('area', 'Area');
        $form->text('device', 'Device');
        $form->switch('block', 'Block');

        return $form;
    }

    public function blockIp()
    {
        return $script=<<<EOT
      
        $('.block').click(function(){
            var id = $(this).data('id')
            window.location.href='/admin/openidblock/'+id; 
        })   
EOT;
    }

    public function block(OpenId $openId)
    {

        $openId->block = !$openId->block;

        $openId->save();

        return redirect('/admin/openId');
    }
}
