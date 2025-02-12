<?php

namespace App\Notifications\Invoices\Admin;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedForAdmin extends Notification implements ShouldQueue
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
            ->subject("New Invoice #{$this->invoice->invoiceNumber} Created")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("A new invoice #{$this->invoice->invoiceNumber} has been created for player: ".getUserFullName($this->invoice->receiverUser))
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: ".convertToDatetime($this->invoice->dueDate))
            ->action('View Invoice here', route('invoices.show', ['invoice' => $this->invoice->hash]))
            ->line("Please review the invoice details in the invoices page. Please follow up the player ".getUserFullName($this->invoice->receiverUser)." to pay the invoice as soon as possible.")
            ->line("If payment is not received by the due date, a reminder will be sent automatically to the player.")
            ->line("You can manage this invoice directly from the invoices page to track its status and take any required action.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "New Invoice has been created",
            'data' =>'A new invoice '.$this->invoice->invoiceNumber.' has been created for '.getUserFullName($this->invoice->receiverUser).'. Please follow up the player to pay the invoice as soon as possible.!',
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoice->hash])
        ];
    }
}
