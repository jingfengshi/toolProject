<?php

namespace App\Admin\Controllers;

use App\Models\Location;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class LocationsController extends Controller
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
            ->header('ip地域管理')
            ->description('ip地域管理')
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
        $grid = new Grid(new Location);


        $grid->column('id');
        $grid->column('ip');
        $grid->column('location','地区');
        $grid->column('device','设备');
        $grid->created_at('创建时间');
        $grid->uodated_at('更新时间');

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
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->like('ip', 'IP');
            $filter->like('location', '地区');
            $filter->in('block','是否拉黑')->radio([
                0    =>'拉黑',
                1    =>'拉白',
            ]);
        });

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
        $show = new Show(Location::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Location);



        return $form;
    }


    public function blockIp()
    {
        return $script=<<<EOT
      
        $('.block').click(function(){
            var id = $(this).data('id')
            window.location.href='/admin/ipblock/'+id; 
        })   
EOT;
    }


    public function block(Location $location)
    {

        $location->block = !$location->block;

        $location->save();

        return redirect('/admin/location');
    }
}
