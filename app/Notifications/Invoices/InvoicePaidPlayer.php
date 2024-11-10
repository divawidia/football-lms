<?php

namespace App\Notifications\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaidPlayer extends Notification
{
    use Queueable;
    protected $invoice;
    protected $playerName;
    /**
     * Create a new notification instance.
     */
    public function __construct($invoice, $playerName)
    {
        $this->invoice = $invoice;
        $this->playerName = $playerName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice Payment Confirmation')
            ->greeting("Hello {$this->playerName},")
            ->line("Thank you for your payment! We have received payment for your invoice #{$this->invoice->invoiceNumber}.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Paid: ".priceFormat($this->invoice->ammountDue))
            ->line("Payment Date: " . now()->toFormattedDateString())
            ->line('Your payment has been successfully processed. You can view the payment details and download a receipt from your account.')
            ->action('View Payment Details', url()->route('billing-and-payments.show', $this->invoice->id))
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
            'data' =>'Thank you! Your payment for Invoice #'.$this->invoice->invoiceNumber.' has been successfully processed.',
            'redirectRoute' => route('billing-and-payments.show', ['invoice' => $this->invoice->id])
        ];
    }
}
