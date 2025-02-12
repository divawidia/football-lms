<?php

namespace App\Notifications\Invoices\Admin;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceArchivedForAdmin extends Notification implements ShouldQueue
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
            ->subject("Invoice #{$this->invoice->invoiceNumber} Archived for player ".getUserFullName($this->invoice->receiverUser))
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("An invoice #{$this->invoice->invoiceNumber} for player : ".getUserFullName($this->invoice->recieverUser)." has been archived.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: ".convertToDatetime($this->invoice->dueDate))
            ->line('This invoice is now stored for reference and will no longer appear in the active invoice list.')
            ->action('View Archived Invoice', route('invoices.show-archived', $this->invoice->hash))
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
            'title' => "Invoice has been archived",
            'data' =>'Invoice #'.$this->invoice->invoiceNumber.' for player '.getUserFullName($this->invoice->recieverUser).' has been archived.',
            'redirectRoute' => route('invoices.show-archived', ['invoice' => $this->invoice->hash])
        ];
    }
}
