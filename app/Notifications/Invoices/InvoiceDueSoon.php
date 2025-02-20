<?php

namespace App\Notifications\Invoices;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceDueSoon extends Notification implements ShouldQueue
{
    use Queueable;
    protected Invoice $invoice;
    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'mail',
            'database'
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Invoice is Due Soon")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName},")
            ->line("This is a friendly reminder that your invoice is due soon. Please ensure payment is made on time.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: ".convertToDatetime($this->invoice->dueDate))
            ->action('View Invoice', route('billing-and-payments.show', $this->invoice->hash))
            ->line('You can review the invoice details and take any necessary actions in the invoices page.')
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Invoice is due soon!",
            'data' =>'Your invoice #'.$this->invoice->invoiceNumber.' is due soon. Please complete your payment before the due date at '.convertToDatetime($this->invoice->dueDate).' to avoid late payment.',
            'redirectRoute' => route('billing-and-payments.show', ['invoice' => $this->invoice->hash])
        ];
    }
}
