<?php

namespace App\Notifications\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaidAdmin extends Notification
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
            ->subject("Player {$this->playerName} Invoice #{$this->invoice->invoiceNumber} Payment Confirmation")
            ->greeting("Dear {$notifiable->name},")
            ->line("A payment has been successfully processed for the following player.")
            ->line("Player Name: {$this->playerName}")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Paid: ".priceFormat($this->invoice->ammountDue))
            ->line("Payment Date: " . now()->toFormattedDateString())
            ->line('The invoice has been marked as paid, and the player’s subscription remains active. You can view the payment details in the admin invoices page.')
            ->action('View Payment Details', url()->route('invoices.show', $this->invoice->id))
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
            'data' =>'Player '.$this->playerName.' has successfully paid Invoice #'.$this->invoice->invoiceNumber.'. Click this notification to view the payment details.',
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoice->id])
        ];
    }
}
