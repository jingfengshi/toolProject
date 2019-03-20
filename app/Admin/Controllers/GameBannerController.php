<?php

namespace App\Admin\Controllers;

use App\Models\GameBanner;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class GameBannerController extends Controller
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
            ->header('轮播图')
            ->description('轮播图')
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
        $grid = new Grid(new GameBanner);

        $grid->id('Id');
        $grid->url('Url')->display(function ($url) {
            if ($url && strlen($url) > 15) {
                return substr($url, 0, 12).'...';
            } else {
                return $url;
            }
        });
        $grid->jumpId('JumpId');
        $grid->ghId('GhId');
        $grid->jumpUrl('JumpUrl')->display(function ($url) {
            if ($url && strlen($url) > 15) {
                return substr($url, 0, 12).'...';
            } else {
                return $url;
            }
        });
        $grid->jumpAppId('JumpAppId');
        $grid->bannerUrl('BannerUrl')->display(function ($url) {
            if ($url && strlen($url) > 15) {
                return substr($url, 0, 12).'...';
            } else {
                return $url;
            }
        });
        $grid->introduce('Introduce');
        $grid->jumpType('JumpType');
        $grid->jumpName('JumpName');
        $grid->display('Display');
        $grid->extraData('ExtraData');
        $grid->clickNub('ClickNub');
        $grid->logo('Logo')->display(function ($url) {
            if ($url && strlen($url) > 15) {
                return substr($url, 0, 12).'...';
            } else {
                return $url;
            }
        });
        $grid->clickRate('ClickRate');
        $grid->tabLogo('TabLogo');
        $grid->sort('Sort');
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
        $show = new Show(GameBanner::findOrFail($id));

        $show->id('Id');
        $show->url('Url');
        $show->jumpId('JumpId');
        $show->ghId('GhId');
        $show->jumpUrl('JumpUrl');
        $show->jumpAppId('JumpAppId');
        $show->bannerUrl('BannerUrl');
        $show->introduce('Introduce');
        $show->jumpType('JumpType');
        $show->jumpName('JumpName');
        $show->display('Display');
        $show->extraData('ExtraData');
        $show->clickNub('ClickNub');
        $show->logo('Logo');
        $show->clickRate('ClickRate');
        $show->tabLogo('TabLogo');
        $show->sort('Sort');
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
        $form = new Form(new GameBanner);

        $form->url('url', 'Url')->required(true);
        $form->text('jumpId', 'JumpId')->required(true);
        $form->text('ghId', 'GhId')->required(true);
        $form->text('jumpUrl', 'JumpUrl');
        $form->text('jumpAppId', 'JumpAppId')->required(true);
        $form->text('bannerUrl', 'BannerUrl')->required(true);
        $form->textarea('introduce', 'Introduce');
        $form->text('jumpType', 'JumpType')->required(true);
        $form->text('jumpName', 'JumpName')->required(true);
        $form->text('display', 'Display')->required(true);
        $form->text('extraData', 'ExtraData');
        $form->text('clickNub', 'ClickNub')->required(true);
        $form->text('logo', 'Logo')->required(true);
        $form->text('clickRate', 'ClickRate')->required(true);
        $form->text('tabLogo', 'TabLogo');
        $form->text('sort', 'Sort');
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
        return redirect('/admin/gamebanner');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $this->form()->destroy($id);
        return redirect('/admin/gamebanner');
    }
}
