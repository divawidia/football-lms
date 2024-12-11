<?php

namespace App\Console;

use App\Console\Commands\Competitions\CompletedCompetitionStatus;
use App\Console\Commands\Competitions\StartCompetitionStatus;
use App\Console\Commands\Invoices\InvoiceDueSoonNotification;
use App\Console\Commands\Invoices\SetPastDueInvoiceStatus;
use App\Console\Commands\Matches\CompletedMatchStatus;
use App\Console\Commands\Matches\MatchReminderNotification;
use App\Console\Commands\Matches\StartMatchStatus;
use App\Console\Commands\Subscriptions\SetRenewSubscription;
use App\Console\Commands\Subscriptions\SubscriptionDueSoonNotification;
use App\Console\Commands\Trainings\CompletedTrainingStatus;
use App\Console\Commands\Trainings\StartTrainingStatus;
use App\Console\Commands\Trainings\TrainingReminderNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\ScheduleMonitor\Models\MonitoredScheduledTaskLogItem;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        CompletedCompetitionStatus::class,
        CompletedTrainingStatus::class,
        CompletedMatchStatus::class,
        InvoiceDueSoonNotification::class,
        MatchReminderNotification::class,
        SetPastDueInvoiceStatus::class,
        StartCompetitionStatus::class,
        StartMatchStatus::class,
        StartTrainingStatus::class,
        SubscriptionDueSoonNotification::class,
        TrainingReminderNotification::class,
        SetRenewSubscription::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {


        $schedule->command('update:start-competition-status')->everyMinute();
        $schedule->command('update:complete-competition-status')->everyMinute();
        $schedule->command('update:set-past-due-invoice-status')->everyMinute();
        $schedule->command('update:invoice-due-soon-notification')->everyMinute();
        $schedule->command('update:subscription-due-soon-notification')->everyMinute();
        $schedule->command('update:training-reminder-notification')->everyMinute();
        $schedule->command('update:match-reminder-notification')->everyMinute();
        $schedule->command('update:start-training-data')->everyMinute();
        $schedule->command('update:start-match-data')->everyMinute();
        $schedule->command('update:completed-training-status')->everyMinute();
        $schedule->command('update:completed-match-status')->everyMinute();
        $schedule->command('update:subscription-renew')->everyMinute();
        $schedule->command('model:prune', ['--model' => MonitoredScheduledTaskLogItem::class])->daily();
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
