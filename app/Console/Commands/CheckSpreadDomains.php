<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckSpreadDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fangfeng:check-spread-domains';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {
        $this->info('入口域名开始检测');


        app(\App\Services\CheckSpreadDomains::class)->handle();

        $this->info('入口域名检测完毕');
    }
}
