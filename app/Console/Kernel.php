<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule HAOS sync commands
        $schedule->command('haos:sync')->everyTwoMinutes();
        $schedule->command('haos:sync --power-outage')->everyFiveSeconds();
        $schedule->command('haos:sync --smoke-sensor')->everyFiveSeconds(); 
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
