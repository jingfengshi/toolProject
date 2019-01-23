<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendWechatMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CZadmin:sendWechatMessage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '群发微信消息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(\App\Services\SendWechatMessage $message)
    {
        // 在命令行打印一行信息
        $this->info("开始发送...");

        $message->sendMessage();

        $this->info("发送成功！");
    }
}
