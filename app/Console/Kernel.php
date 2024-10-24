<?php

namespace App\Console;

use App\Console\Commands\DeactivateTrainingStatus;
use App\Console\Commands\EndCompetitionStatus;
use App\Console\Commands\EndMatchStatus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        DeactivateTrainingStatus::class,
        EndMatchStatus::class,
        EndCompetitionStatus::class,
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
         $schedule->command('update:training-status-data')->everyFiveMinutes();
         $schedule->command('update:end-match-status')->everyFiveMinutes();
         $schedule->command('update:end-competition-status')->everyFiveMinutes();
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
