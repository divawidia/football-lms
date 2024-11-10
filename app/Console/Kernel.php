<?php

namespace App\Console;

use App\Console\Commands\DeactivateTrainingStatus;
use App\Console\Commands\EndCompetitionStatus;
use App\Console\Commands\EndMatchStatus;
use App\Console\Commands\SetPastDueInvoiceStatus;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Notifications\Invoices\InvoiceDueSoon;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderAdmin;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderPlayer;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Notification;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        DeactivateTrainingStatus::class,
        EndMatchStatus::class,
        EndCompetitionStatus::class,
        SetPastDueInvoiceStatus::class,
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('update:training-status-data')->everyMinute();
        $schedule->command('update:end-match-status')->everyMinute();
        $schedule->command('update:end-competition-status')->everyMinute();
        $schedule->command('update:set-past-due-invoice-status')->everyMinute();
        $schedule->command('update:invoice-due-soon-notification')->everyMinute();
        $schedule->command('update:subscription-due-soon-notification')->everyMinute();
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
