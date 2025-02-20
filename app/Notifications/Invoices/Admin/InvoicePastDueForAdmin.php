<?php

namespace App\Notifications\Invoices\Admin;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePastDueForAdmin extends Notification implements ShouldQueue
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
            ->subject("Invoice is Past Due")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName},")
            ->line("An invoice #{$this->invoice->invoiceNumber} is now past due.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Player Name: ". getUserFullName($this->invoice->receiverUser))
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: ".convertToDatetime($this->invoice->dueDate))
            ->action('View Invoice Details', route('invoices.show', $this->invoice->hash))
            ->line('Please follow up the player as necessary or take appropriate action.')
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
            'title' => "Invoice is Past Due",
            'data' =>'Invoice #'.$this->invoice->invoiceNumber.' for player '.getUserFullName($this->invoice->receiverUser).' is now past due at '.convertToDatetime($this->invoice->dueDate).'. Please reach the player and recreate the invoice to settle the payment at the earliest convenience. Click here to see the invoice!',
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoice->hash])
        ];
    }
}
