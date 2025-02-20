<?php

namespace App\Console\Commands\Invoices;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Console\Command;

class SetInvoiceStatusByMidtrans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:set-invoice-status-by-midtrans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set invoice status by midtrans transaction record status';

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
        // Update records where end_date is less than the current date
        $invoices = Invoice::where('status', 'Open')->get();
        foreach ($invoices as $invoice){
            $this->invoiceService->checkMidtransInvoiceStatus($invoice);
        }

        $this->info('Invoice status data updated successfully to past due.');
    }
}
