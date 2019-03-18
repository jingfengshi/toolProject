<?php

namespace App\Admin\Controllers;

use App\Models\WxShortDomain;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class WxShortDomainController extends Controller
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
//    protected function grid()
//    {
//        $grid = new Grid(new WxShortDomain);
//
//        $grid->id('Id');
//        $grid->ip('Ip');
//        $grid->origin_url('Origin url');
//        $grid->short_url('Short url');
//        $grid->validate_time('Validate time');
//        $grid->admin_user_id('Admin user id');
//        $grid->created_at('Created at');
//        $grid->updated_at('Updated at');
//
//        return $grid;
//    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WxShortDomain::findOrFail($id));

        $show->id('Id');
        $show->ip('Ip');
        $show->origin_url('Origin url');
        $show->short_url('Short url');
        $show->admin_user_id('Admin user id');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @param Request $request
     * @param Content $content
     * @return Content
     */
//    protected function form()
//    {
//        $form = new Form(new WxShortDomain);
//
//        $form->ip('ip', 'Ip');
//        $form->textarea('origin_url', 'Origin url');
//        $form->text('short_url', 'Short url');
//        $form->date('validate_time', 'Validate time')->default(date('Y-m-d'));
//        $form->number('admin_user_id', 'Admin user id');
//
//        return $form;
//    }

    public function wxShortDomain(Request $request, Content $content)
    {
        $content->header('短域名管理');
        $content->description('将长域名转为短域名');
        $content->body($this->form($request));
        return $content->body($this->grid());
    }


    public function store(Request $request)
    {
        $this->form($request)->store();
        return redirect('/admin/wxshortdomain');
    }


    protected function grid()
    {
        $grid = new Grid(new WxShortDomain);
        $grid->id('ID')->sortable();
        $grid->ip('IP');
        $grid->origin_url('原始链接');
        $grid->column('short_url')->display(function ($short_url) {
            return "<input readonly value=" . $short_url . " id='s_url'" . "style='width:300px'" . " >" . "<button style='display: inline-block' class='btn-primary copy'>复制</button>";
        });

        $grid->column('creator.name', '创建用户');
        $grid->created_at('创建时间')->sortable();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableActions();

        if (Admin::user()->id === 1) {
            $grid->model()->orderBy('id', 'desc');
        } else {
            $grid->model()->where('admin_user_id', Admin::user()->id)->orderBy('id', 'desc');
        }
        Admin::script("$('.copy').click(function(){
                $(this).prev().select();
                document.execCommand(\"Copy\"); // 执行浏览器复制命令
        })");
        return $grid;
    }


    protected function form(Request $request)
    {
        $form = new Form(new WxShortDomain());
        $form->setAction('/admin/wxshortdomain');
        $form->text('origin_url', '客户原始链接')->rules('required|url');
        $form->number('generate_number', '产生个数')->max(100)->rules('required');
        $form->hidden('ip');
        $form->hidden('short_url');
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->saving(function (Form $form) use ($request) {
            $access_token = $this->getAccessToken();
            $data = '{"action":"long2short","long_url":' . '"' . $form->origin_url . '"}';
            $url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token={$access_token}";

            for ($i = 0; $i < $form->generate_number; $i++) {
                $res = $this->requestPost($url, $data);
                $form->short_url .= $res['short_url'] . ',';
            }
            $form->short_url = mb_substr($form->short_url, 0, mb_strlen($form->short_url) - 1);
            $form->ip = $request->getClientIp();
            $form->model()->admin_user_id = Admin::user()->id;
            return $form;
        });

        return $form;
    }

    /**
     * 获取access_token
     * @param string $token_file
     * @return bool|false|string
     */
    public function getAccessToken($token_file = './access_token')
    {
        // 考虑过期问题，将获取的access_token存储到某个文件中
        $life_time = 7200;
        if (file_exists($token_file) && time() - filemtime($token_file) < $life_time) {
            // 存在有效的access_token
            return file_get_contents($token_file);
        }
        // 目标URL：
        $wechat = config('wechat.official_account.default');
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$wechat['app_id']}&secret={$wechat['secret']}";

        //向该URL，发送GET请求
        $result = $this->requestGet($url);
        if (!$result) {
            return false;
        }

        // 写入
        file_put_contents($token_file, $result['access_token']);
        return $result->access_token;
    }

    /**
     * 获取post请求数据
     * @param $url
     * @param $params
     * @return mixed
     */
    public function requestPost($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . strlen($params)));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    /**
     * get请求
     * @param string $url
     * @return mixed
     */
    private function requestGet(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $res = curl_exec($ch);
        return json_decode($res, true);
    }
}
