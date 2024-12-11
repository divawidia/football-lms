<?php

namespace App\Notifications\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePastDueAdmin extends Notification implements ShouldQueue
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
            ->subject("Invoice #{$this->invoice->invoiceNumber} is Past Due for Player ($this->playerName)")
            ->greeting("Hello Admins,")
            ->line("An invoice #{$this->invoice->invoiceNumber} is now past due.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: ".convertToDatetime($this->invoice->dueDate))
            ->action('View Invoice Details', route('invoices.show', $this->invoice->id))
            ->line('Please follow up the player as necessary or take appropriate action.')
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
            'data' =>'Invoice #'.$this->invoice->invoiceNumber.' for player '.$this->playerName.' is now past due at '.convertToDatetime($this->invoice->dueDate).'. Please reach the player and recreate the invoice to settle the payment at the earliest convenience. Click here to see the invoice!',
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoice->id])
        ];
    }
}
