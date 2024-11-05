<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaidPlayer extends Notification
{
    use Queueable;
    protected $invoiceId;
    protected $invoiceNumber;
    /**
     * Create a new notification instance.
     */
    public function __construct($invoiceId, $invoiceNumber)
    {
        $this->invoiceId = $invoiceId;
        $this->invoiceNumber = $invoiceNumber;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' =>'Thank you! Your payment for Invoice #'.$this->invoiceNumber.' has been successfully processed.',
            'redirectRoute' => route('billing-and-payments.show', ['invoice' => $this->invoiceId])
        ];
    }
}
