<?php

namespace App\Notifications\Invoices\Player;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaidForPlayer extends Notification implements ShouldQueue
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
            ->subject('Invoice Successfully Paid')
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName},")
            ->line("Your payment has been successfully processed. Thank you for your payment! We have received payment for your invoice #{$this->invoice->invoiceNumber}.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Paid: ".priceFormat($this->invoice->ammountDue))
            ->line("Payment Date: " . now()->toFormattedDateString())
            ->line("You can view the payment details and download a receipt from your account.")
            ->action('View Payment Details', route('billing-and-payments.show', $this->invoice->hash))
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
            'title' => "Invoice has been successfully paid",
            'data' =>'Thank you! Your payment for Invoice #'.$this->invoice->invoiceNumber.' has been successfully paid/processed.',
            'redirectRoute' => route('billing-and-payments.show', ['invoice' => $this->invoice->hash])
        ];
    }
}
