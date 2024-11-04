<?php

namespace App\Console;

use App\Console\Commands\DeactivateTrainingStatus;
use App\Console\Commands\EndCompetitionStatus;
use App\Console\Commands\EndMatchStatus;
use App\Console\Commands\SetPastDueInvoiceStatus;
use App\Models\Invoice;
use App\Notifications\InvoiceDueSoon;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

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
        $schedule->call(function () {
            $invoices = Invoice::where('dueDate', '=', Carbon::now()->addHour())->where('status', 'Open')->get();
            foreach ($invoices as $invoice) {
                $invoice->receiverUser->notify(new InvoiceDueSoon($invoice->dueDate, $invoice->id));
            }
        })->everyMinute();
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
