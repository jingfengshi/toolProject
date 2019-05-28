<?php

namespace App\Console;

use App\Console\Commands\FillMiniProData;
use App\Console\Commands\ImportGames;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ImportGames::class,
        FillMiniProData::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
//        $schedule->command('CZadmin:sendWechatMessage')->everyMinute();
//        //每天三点执行
//        $schedule->command('command:fillminiprodata')->dailyAt('07:00');
//        //每周一三点执行
//        $schedule->command('command:fillminiprodataweekly')->weekly()->mondays()->at('08:00');
//        //每月一号三点执行
//        $schedule->command('command:fillminiprodatamonthly')->monthlyOn(1, '09:00');

        $schedule->command('fangfeng:check-spread-domains')->everyMinute();
        $schedule->command('fangfeng:check-land-domains')->everyMinute();
        $schedule->command('fangfeng:check-auth-domains')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
