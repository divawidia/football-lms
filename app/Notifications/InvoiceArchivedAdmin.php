<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceArchivedAdmin extends Notification
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
            ->subject("Invoice #{$this->invoice->invoiceNumber} Archived for player {$this->playerName}")
            ->greeting("Hello Teams,")
            ->line("An invoice #{$this->invoice->invoiceNumber} for player {$this->playerName} has been archived.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: ".convertToDatetime($this->invoice->dueDate))
            ->line('This invoice is now stored for reference and will no longer appear in the active invoice list.')
            ->action('View Archived Invoice', url()->route('invoices.show-archived', $this->invoice->id))
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
            'data' =>'Invoice #'.$this->invoice->invoiceNumber.' for player '.$this->playerName.' has been archived.',
            'redirectRoute' => route('invoices.show-archived', ['invoice' => $this->invoice->id])
        ];
    }
}
