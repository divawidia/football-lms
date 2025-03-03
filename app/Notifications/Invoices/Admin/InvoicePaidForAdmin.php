<?php

namespace App\Notifications\Invoices\Admin;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaidForAdmin extends Notification implements ShouldQueue
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
            ->subject("Invoice Has Been Successfully Paid")
            ->greeting("Dear {$notifiable->firstName} {$notifiable->lastName},")
            ->line("A payment has been successfully processed for the following player.")
            ->line("Player Name: ". getUserFullName($this->invoice->receiverUser))
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Paid: ".priceFormat($this->invoice->ammountDue))
            ->line("Payment Date: " . now()->toFormattedDateString())
            ->line('The invoice has been marked as paid, and the player’s subscription remains active. You can view the payment details in the admin invoices page.')
            ->action('View Payment Details', url()->route('invoices.show', $this->invoice->hash))
            ->line('If you have any questions or need further assistance, please let us know.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Invoice has been successfully paid",
            'data' =>'Player : '. getUserFullName($this->invoice->receiverUser).' has successfully paid Invoice #'.$this->invoice->invoiceNumber.'. Click this notification to view the payment details.',
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoice->hash])
        ];
    }
}
