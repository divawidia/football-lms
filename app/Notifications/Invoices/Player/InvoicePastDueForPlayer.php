<?php

namespace App\Notifications\Invoices\Player;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePastDueForPlayer extends Notification implements ShouldQueue
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
            ->subject("Invoice #{$this->invoice->invoiceNumber} is Past Due")
            ->greeting("Dear {$notifiable->firstName} {$notifiable->lastName},")
            ->line("Your invoice #{$this->invoice->invoiceNumber} is now past due.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: ".convertToDatetime($this->invoice->dueDate))
            ->action('View Payment Details', route('billing-and-payments.show', $this->invoice->hash))
            ->line('Please reach our admins to recreate the invoice to settle the payment at your earliest convenience.')
            ->line('If you have any questions, feel free to reach out to our support team.')
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
            'title' => "Your Invoice is Past Due",
            'data' =>'Your invoice #'.$this->invoice->invoiceNumber.' is now past due at '.convertToDatetime($this->invoice->dueDate).'. Please reach our admins to recreate the invoice to settle the payment at your earliest convenience. Click here to see the invoice!',
            'redirectRoute' => route('billing-and-payments.show', ['invoice' => $this->invoice->hash])
        ];
    }
}
