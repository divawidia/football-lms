<?php

namespace App\Console\Commands\Invoices;

use App\Models\Invoice;
use App\Notifications\Invoices\InvoiceDueSoon;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InvoiceDueSoonNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:invoice-due-soon-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent reminder notification to players with due soon invoice';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Update records where end_date is less than the current date
        $invoices = Invoice::whereBetween('dueDate', [Carbon::now()->addHour(), Carbon::now()->addHour()->addMinutes(5)])
            ->where('status', 'Open')
            ->where('isReminderNotified', '0')
            ->get();
        foreach ($invoices as $invoice) {
            $invoice->update(['isReminderNotified' => '1']);
            $invoice->receiverUser->notify(new InvoiceDueSoon($invoice));
        }

        $this->info('Player with due soon invoices successfully sent notification.');
    }
}
