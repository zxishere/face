<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Redis;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
/*        $$schedule->command('inspire')
                 ->everyMinute();*/
        $schedule->command('staffs:update')
                ->dailyAt('23:59');
        $schedule->command('staffs:check')
                ->weekdays()
                ->between('7:00', '22:00')
                ->everyMinute()
                ->withoutOverlapping()
                ->sendOutputTo(storage_path('app/public'). "/staffs.txt")->when(function () {
                    Redis::get('staffs') > 0;
                });
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
