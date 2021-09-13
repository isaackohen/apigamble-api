<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\PaymentCallbackQueue;
use App\Console\Commands\SendCallbackinfo;
use App\Console\Commands\PaymentBalancesUpdate;

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

        $schedule->command(PaymentCallbackQueue::class)->everyMinute();
        $schedule->command(PaymentBalancesUpdate::class)->Daily();
        $schedule->command(SendCallbackinfo::class)->everyTenMinutes();
		$schedule->command('check:duty all')->cron('0 0 */2 * *');

        
        // $schedule->command('inspire')->hourly();
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
