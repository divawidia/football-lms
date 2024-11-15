<?php

namespace App\Console;

use App\Console\Commands\CompletedCompetitionStatus;
use App\Console\Commands\DeactivateTrainingStatus;
use App\Console\Commands\InvoiceDueSoonNotification;
use App\Console\Commands\MatchReminderNotification;
use App\Console\Commands\StartCompetitionStatus;
use App\Console\Commands\EndMatchStatus;
use App\Console\Commands\SetPastDueInvoiceStatus;
use App\Console\Commands\SubscriptionDueSoonNotification;
use App\Console\Commands\TrainingReminderNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
    protected $commands = [
        CompletedCompetitionStatus::class,
        DeactivateTrainingStatus::class,
        EndMatchStatus::class,
        InvoiceDueSoonNotification::class,
        MatchReminderNotification::class,
        SetPastDueInvoiceStatus::class,
        StartCompetitionStatus::class,
        SubscriptionDueSoonNotification::class,
        TrainingReminderNotification::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('update:training-status-data')->everyMinute();
        $schedule->command('update:end-match-status')->everyMinute();
        $schedule->command('update:start-competition-status')->everyMinute();
        $schedule->command('update:complete-competition-status')->everyMinute();
        $schedule->command('update:set-past-due-invoice-status')->everyMinute();
        $schedule->command('update:invoice-due-soon-notification')->everyMinute();
        $schedule->command('update:subscription-due-soon-notification')->everyMinute();
        $schedule->command('update:training-reminder-notification')->everyMinute();
        $schedule->command('update:match-reminder-notification')->everyMinute();
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
