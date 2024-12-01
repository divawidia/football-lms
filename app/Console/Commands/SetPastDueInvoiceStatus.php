<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetPastDueInvoiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:set-past-due-invoice-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set past due invoice status records where the due date has passed';

    private InvoiceService $invoiceService;
    public function __construct(InvoiceService $invoiceService)
    {
        parent::__construct();
        $this->invoiceService = $invoiceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current date and time
        $now = Carbon::now();

        // Update records where end_date is less than the current date
        $invoices = Invoice::whereDate('dueDate', '<=', $now)->where('status', 'Open')->get();
        foreach ($invoices as $invoice){
            $this->invoiceService->pastDue($invoice);
        }

        $this->info('Invoice status data updated successfully to past due.');
    }
}
