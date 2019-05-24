<?php
/**
 * Created by PhpStorm.
 * User: jasper
 * Date: 2019/1/14
 * Time: 11:41
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CompressSite;
use App\Models\ShortDomain;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompressSitesController extends Controller
{

    public function index(Request $request,Content $content)
    {
        $content->header('页面压缩');
        $content->description('防止网页过期');
        $content->body($this->form($request));
        $content->body($this->grid());
        return $content;
    }


    public function store(Request $request)
    {
        $this->form($request)->store();
        return redirect('/admin/compress');
    }

    public  function form(Request $request)
    {
        $form = new Form(new CompressSite());
        $form->setAction('/admin/compress');
        $form->text('wechat_url', '微信页面url')->rules('required|url');
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->saving(function(Form $form)use($request){

            $url=$request->wechat_url;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt ( $ch, CURLOPT_HEADER, 0 );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            $res = curl_exec($ch);
            // 关闭cURL资源，并且释放系统资源
            curl_close($ch);
            $res = $this->urlChange($res, $url);
            $name =date("Ym/d", time()).'/'. md5($url).'.html';

            $local_url=Storage::disk('sites')->put($name,$res);
            $form->model()->admin_user_id=Admin::user()->id;
            $form->model()->local_url=Storage::disk('sites')->url($name);
        });
        return $form;
    }


    public function grid()
    {
        $grid = new Grid(new compressSite());
        $grid->id('ID')->sortable();
        $grid->wechat_url('微信原始url');
        $grid->local_url('本地url');
        $grid->created_at('创建时间')->sortable();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableActions();
        $grid->model()->orderBy('id','desc');
        return $grid;
    }

    protected function urlChange($str, $url)
    {
        $str = preg_replace('/<div class="rich_media_global_msg">(.*)此为临时链接，仅用于预览，将在短期内失效。(.*)<a id="js_close_temp" href="##" class="icon_closed">关闭<\/a>(.*)<\/div>/Us', '',$str);
        /*		$str = preg_replace_callback('/data-src="(.*)"/Us', function ($matches) {
                    return 'data-src="http://localhost/image.php?url='.$matches[1].'"';
                }, $str);*/
        $host = $_SERVER['HTTP_HOST'];
        // $str = preg_replace('/<img (.*) data-src="(.*)" (.*)>/Us', '<img ${1} data-src="http://'.$host.'/image.php?url=${2}" ${3}>', $str);
        $str = preg_replace('/(https:\/\/mmbiz\.qpic\.cn)/Us', 'http://'.$host.'/wechat_image?url=${1}', $str);

        // 引用相对路径的图片
        $str = preg_replace_callback('/<img (.*) src="(.*?)" (.*?)>/Us', function ($matches) use ($url, $host) {
            \Log::info(json_encode($matches[2]));
            if (stripos($matches[2], 'http') !== 0) {
                $matches[2] = "http://$host/wechat_image?url=".$url.$matches[2];

            }



            return '<img '.$matches[1].' src="'.$matches[2].'" '.$matches[3].'>';
        }, $str);

        return $str;
    }

}