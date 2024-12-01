<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Notifications\Invoices\InvoiceDueSoon;
use App\Services\InvoiceService;
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
        $invoices = Invoice::whereDate('dueDate', '=', Carbon::now()->addHour())->where('status', 'Open')->get();
        foreach ($invoices as $invoice) {
            $playerName = $invoice->receiverUser->firstName.' '.$invoice->receiverUser->lastName;
            $invoice->receiverUser->notify(new InvoiceDueSoon($invoice, $playerName));
        }

        $this->info('Player with due soon invoices successfully sent notification.');
    }
}
