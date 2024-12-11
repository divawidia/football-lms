<?php

namespace App\Notifications\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedAdmin extends Notification implements ShouldQueue
{
    use Queueable;
    protected $invoice;
    protected $playerName;

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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Invoice #{$this->invoice->invoiceNumber} Created for Player {$this->playerName}")
            ->line("A new invoice #{$this->invoice->invoiceNumber} has been created for player: {$this->playerName}.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: {$this->invoice->dueDate}")
            ->action('View Invoice here', route('invoices.show', ['invoice' => $this->invoice->id]))
            ->line("Please review the invoice details in the invoices page. Please follow up the player {$this->playerName} to pay the invoice as soon as possible.")
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
            'data' =>'💼 A new invoice '.$this->invoice->invoiceNumber.' has been created for '.$this->playerName.'. Please follow up the player to pay the invoice as soon as possible.!',
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoice->id])
        ];
    }
}
