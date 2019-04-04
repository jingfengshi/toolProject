<?php

namespace App\Admin\Controllers;

use App\Models\DomainRegistration;
use App\Http\Controllers\Controller;
use App\Services\AliyunUtils;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use function MongoDB\Driver\Monitoring\removeSubscriber;

class DomainRegistrationController extends Controller
{
    use HasResourceActions;

    public function domainRegistration(Request $request, Content $content)
    {
        $content->header('域名注册');
        $content->description('域名批量注册');
        $content->body($this->form($request));
        return $content->body($this->grid());
    }

    public function store(Request $request)
    {
        set_time_limit(0);
        $this->form($request)->store();
        return redirect('/admin/domainregistration');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DomainRegistration);

        $grid->id('Id');
        $grid->domain('域名');
        $grid->full_domain('全域名');
        $grid->rr('主机记录');
        $grid->status('注册成功')->display(
            function ($status) {
                if ($status) {
                    return '是';
                } else {
                    return '否';
                }
            }
        );
        $grid->ip('IP');
        $grid->taskno('批量注册码');
        $grid->task_status_code('注册状态码');
        $grid->errormsg('注册信息');
        $grid->dns_taskid('解析任务码');
        $grid->created_at('创建时间');
//        $grid->updated_at('Updated at');
        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->model()->orderBy('id', 'desc');
        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @param Request $request
     * @return Form
     */
    protected function form(Request $request)
    {
        $form = new Form(new DomainRegistration);
        $form->setAction('/admin/domainregistration');
        $form->hidden('domain');
        $form->hidden('full_domain');
        $form->hidden('rr');
        $form->hidden('status');
        $form->ip('ip', '解析ip')->rules('required|ip');
        $form->hidden('taskno');
        $form->hidden('task_status_code');
        $form->hidden('errormsg');
        $form->hidden('dns_taskid');
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->number('number', '注册数量')->rules('required')->min(1)->max(100);
        $form->saving(function (Form $form) use ($request) {
            $domains = $this->createDomains($form->number);
            //提交批量注册
            $saveResult = AliyunUtils::saveBatchTaskForCreatingOrderActivate($domains);
            Log::info('批量注册', $saveResult);
            //根据注册码获取具体注册信息
            if ($saveResult && $saveResult['TaskNo']) {
                //直到所有任务执行结束，跳出循环体
                while (true) {
                    $pullResult = AliyunUtils::pollTaskResult($saveResult['TaskNo']);
                    Log::info('批量注册执行情况', $pullResult);
                    $tasking = false;//任务执行完标记
                    if ($pullResult && $pullResult['Data'] && $pullResult['Data']['TaskDetail']) {
                        $taskDetails = $pullResult['Data']['TaskDetail'];
                        foreach ($taskDetails as $detail) {
                            if ($detail['TaskStatusCode'] === 0 || $detail['TaskStatusCode'] === 1) {
                                $tasking = true;
                                break;
                            }
                        }
                        if ($tasking) {
                            continue;
                        } else {
                            break;
                        }

                    } else {
                        continue;
                    }
                }

                if ($pullResult && $pullResult['Data'] && $pullResult['Data']['TaskDetail']) {
                    $taskDetails = $pullResult['Data']['TaskDetail'];
                    foreach ($taskDetails as $key => $detail) {
                        if ($detail['TaskStatusCode'] === 2) {
                            //注册成功
                            $taskDetails[$key]['status'] = 1;
                        } elseif ($detail['TaskStatusCode'] === 3) {
                            //注册失败
                            $taskDetails[$key]['status'] = 0;
                        }
                    }
                }

                //批量解析
                $datas = AliyunUtils::operateBatchDomain($taskDetails, $form->ip);
                $insertDatas = [];
                foreach ($datas['domain'] as $data) {
                    $insertdata = [];
                    $insertdata['domain'] = $data['DomainName'];
                    $insertdata['full_domain'] = $data['rr'] . '.' . $data['DomainName'];
                    $insertdata['status'] = $data['status'];
                    $insertdata['number'] = $form->number;
                    $insertdata['taskno'] = $saveResult['TaskNo'];
                    $insertdata['task_status_code'] = $data['TaskStatusCode'];
                    $insertdata['errormsg'] = $data['ErrorMsg'];
                    $insertdata['rr'] = $data['rr'];
                    $insertdata['ip'] = $data['ip'];
                    $insertdata['dns_taskid'] = $datas['dns_taskid'];
                    $insertdata['created_at'] = date('Y-m-d H:i:s');
                    $insertDatas[] = $insertdata;
                }
                DB::table('domain_registration')->insert($insertDatas);
            } else {
                $error = new MessageBag([
                    'title' => '提示',
                    'message' => '执行失败，请重试',
                ]);
                return back()->with(compact('error'));
            }
            $success = new MessageBag([
                'title' => '提示',
                'message' => '执行成功',
            ]);
            return back()->with(compact('success'));
        });
        $form->disableEditingCheck();

        $form->disableCreatingCheck();

        $form->disableViewCheck();
        return $form;
    }

    /**
     * 产生指定数量的可注册域名
     * @param $number
     * @return array
     */
    private function createDomains($number)
    {
        $domains = [];
        for ($i = 0; $i < $number; $i++) {
            while (true) {
                $tmp = AliyunUtils::str_rand(4, 'abcdefghijklmnopqrstuvwxyz');
                $tmp .= AliyunUtils::str_rand(1, '0123456789') . '.' . 'cn';
                if (in_array($tmp, $domains)) {
                    continue;
                } else {
                    $result = AliyunUtils::checkDomain($tmp);
                    if ($result && $result['Avail'] === 1) {
                        $domains[] = $tmp;
                        break;
                    } else {
                        continue;
                    }
                }
            }
        }
        return $domains;
    }
}
