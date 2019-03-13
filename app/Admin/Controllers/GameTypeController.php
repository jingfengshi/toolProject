<?php

namespace App\Admin\Controllers;

use App\Models\GameType;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class GameTypeController extends Controller
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
            ->header('Index')
            ->description('description')
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
        $grid = new Grid(new GameType);

        $grid->id('Id');
        $grid->type('Type');
        $grid->typeValue('TypeValue')->display(function ($typeValue) {
            switch ($typeValue) {
                case 1 :
                    return '小游戏精选';
                case 2:
                    return '热门小游戏';
                case 3:
                    return '独家代理';
                case 4:
                    return '热门游戏推荐';
            }
        });
        $grid->typeLogo('TypeLogo');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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
        $show = new Show(GameType::findOrFail($id));

        $show->id('Id');
        $show->type('Type');
        $show->typeValue('TypeValue')->as(function ($typeValue) {
            switch ($typeValue) {
                case 1 :
                    return '小游戏精选';
                case 2:
                    return '热门小游戏';
                case 3:
                    return '独家代理';
                case 4:
                    return '热门游戏推荐';
            }
        });
        $show->typeLogo('TypeLogo');
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
        $form = new Form(new GameType);

        $form->text('type', 'Type');
        $form->select('typeValue')->options(['1' => '小游戏精选', '2' => '热门小游戏', '3' => '独家代理', '4' => '热门游戏推荐']);
        $form->text('typeLogo', 'TypeLogo');

        return $form;
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        $this->form()->update($id, $request->all());
        return redirect('/admin/gametype');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $this->form()->destroy($id);
        return redirect('/admin/gametype');
    }
}
