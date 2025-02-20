<?php

namespace App\Notifications\Invoices\Admin;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceUncollectibleForAdmin extends Notification implements ShouldQueue
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
            ->subject("Invoice Has Been Marked as Uncollectible")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName},")
            ->line("The following player’s invoice has been marked as uncollectible.")
            ->line("Player Name: ". getUserFullName($this->invoice->receiverUser))
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Original Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Status: Uncollectible")
            ->action('View Invoice Details', url()->route('invoices.show', $this->invoice->hash))
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
            'title' => "Invoice has been marked as uncollectible.",
            'data' =>'The invoice #'.$this->invoice->invoiceNumber.' for player '.getUserFullName($this->invoice->receiverUser).' has been marked as uncollectible. Click here to see the invoice!',
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoice->hash])
        ];
    }
}
