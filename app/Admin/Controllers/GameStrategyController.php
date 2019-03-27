<?php

namespace App\Admin\Controllers;

use App\Models\GameStrategy;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

/**
 * 后台使用 游戏攻略
 * Class GameStrategyController
 * @package App\Admin\Controllers
 */
class GameStrategyController extends Controller
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
            ->header('攻略')
            ->description('游戏攻略')
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
        $grid = new Grid(new GameStrategy);

        $grid->id('Id')->sortable();
        $grid->appletId('AppletId');
        $grid->titleImg('TitleImg')->display(function ($url) {
            if ($url && strlen($url) > 15) {
                return substr($url, 0, 12).'...';
            } else {
                return $url;
            }
        });;
        $grid->titleName('TitleName');
        $grid->content('Content')->display(function ($content) {
            if ($content && mb_strlen($content) > 15) {
                return htmlspecialchars(mb_substr($content, 0, 12)).'...';
            } else {
                return $content;
            }
        });
//        $grid->created_at('Created at');
//        $grid->updated_at('Updated at');
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
        $show = new Show(GameStrategy::findOrFail($id));

        $show->id('Id');
        $show->appletId('AppletId');
        $show->titleImg('TitleImg');
        $show->titleName('TitleName');
        $show->content('Content');
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
        $form = new Form(new GameStrategy);

        $form->text('appletId', 'AppletId');
        $form->image('titleImg', 'TitleImg');
        $form->text('titleName', 'TitleName')->required(true);
       // $form->summernote('content')->attribute(['id'=>'myid']);
        $form->ueditor('content', 'content');
        return $form;
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        $this->form()->update($id,$request->all());
        return redirect('/admin/gamestrategy');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $this->form()->destroy($id);
        return redirect('/admin/gamestrategy');
    }


}
