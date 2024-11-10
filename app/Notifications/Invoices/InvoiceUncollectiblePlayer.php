<?php

namespace App\Notifications\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceUncollectiblePlayer extends Notification
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
            ->subject("Invoice #{$this->invoice->invoiceNumber} Update: Marked as Uncollectible")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your outstanding invoice has been marked as uncollectible.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Original Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Status: Uncollectible")
            ->action('View Payment Details', route('billing-and-payments.show', $this->invoice->id))
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
            'data' =>'Your invoice #'.$this->invoice->invoiceNumber.' has been marked as uncollectible. Click here to see the invoice!',
            'redirectRoute' => route('billing-and-payments.show', ['invoice' => $this->invoice->id])
        ];
    }
}
