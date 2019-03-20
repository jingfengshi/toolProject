<?php

namespace App\Admin\Controllers;

use App\Models\Games;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class GamesController extends Controller
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
            ->header('游戏')
            ->description('游戏列表')
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
        $grid = new Grid(new Games);

        $grid->id('Id');
        $grid->location_index('Location index');
        $grid->type('Type');
        $grid->ghId('GhId');
        $grid->jumpId('JumpId');
        $grid->typeId('TypeId');
        $grid->jumpAppId('JumpAppId');
        $grid->clickNub('ClickNub');
        $grid->introduce('Introduce');
        $grid->logo('Logo')->display(function ($content) {
            if ($content && mb_strlen($content) > 15) {
                return mb_substr($content, 0, 12).'...';
            } else {
                return $content;
            }
        });
        $grid->jumpName('JumpName');
        $grid->aliasName('AliasName');
        $grid->jumpType('JumpType');
        $grid->extraData('ExtraData');
        $grid->jumpGhId('JumpGhId');
//        $grid->created_at('Created at');
//        $grid->updated_at('Updated at');

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
        $show = new Show(Games::findOrFail($id));

        $show->id('Id');
        $show->location_index('Location index');
        $show->type('Type')->as(function ($type) {
            switch ($type) {
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
        $show->ghId('GhId');
        $show->jumpId('JumpId');
        $show->typeId('TypeId');
        $show->jumpAppId('JumpAppId');
        $show->clickNub('ClickNub');
        $show->introduce('Introduce');
        $show->logo('Logo');
        $show->jumpName('JumpName');
        $show->aliasName('AliasName');
        $show->jumpType('JumpType');
        $show->extraData('ExtraData');
        $show->jumpGhId('JumpGhId');
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
        $form = new Form(new Games);

        $form->text('location_index', 'Location index')->required(true);
        $form->select('type')->options(['1' => '小游戏精选', '2' => '热门小游戏', '3' => '独家代理', '4' => '热门游戏推荐'])->required(true);
        $form->text('ghId', 'GhId')->required(true);
        $form->text('jumpId', 'JumpId')->required(true);
        $form->text('typeId','TypeId')->required(true);
        $form->text('jumpAppId', 'JumpAppId')->required(true);
        $form->text('clickNub', 'ClickNub')->required(true);
        $form->textarea('introduce', 'Introduce');
        $form->text('logo', 'Logo')->required(true);
        $form->text('jumpName', 'JumpName')->required(true);
        $form->text('aliasName', 'AliasName')->required(true);
        $form->text('jumpType', 'JumpType')->required(true);
        $form->textarea('extraData', 'ExtraData');
        $form->text('jumpGhId', 'JumpGhId')->required(true);

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
        return redirect('/admin/games');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $this->form()->destroy($id);
        return redirect('/admin/games');
    }
}
