<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Notifications\InvoicePastDueAdmin;
use App\Notifications\InvoicePastDuePlayer;
use App\Repository\UserRepository;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

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
    private UserRepository $userRepository;

    public function __construct(InvoiceService $invoiceService, UserRepository $userRepository)
    {
        parent::__construct();
        $this->invoiceService = $invoiceService;
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current date and time
        $now = Carbon::now();

        // Update records where end_date is less than the current date
        $invoices = Invoice::where('dueDate', '<=', $now)->where('status', 'Open')->get();
        foreach ($invoices as $invoice){
            $this->invoiceService->pastDue($invoice);
        }

        $this->info('Invoice status data updated successfully to past due.');
    }
}
