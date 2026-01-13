<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Train AI recommendation model mỗi ngày lúc 2h sáng
        $schedule->command('recommendation:train')
                 ->dailyAt('02:00')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/recommendation-training.log'));

        // Hoặc chạy mỗi 6 tiếng nếu muốn cập nhật thường xuyên hơn
        // $schedule->command('recommendation:train')
        //          ->everySixHours()
        //          ->withoutOverlapping()
        //          ->runInBackground();
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
