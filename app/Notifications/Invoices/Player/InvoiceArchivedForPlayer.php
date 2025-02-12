<?php

namespace App\Notifications\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceArchivedPlayer extends Notification implements ShouldQueue
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
            ->subject("Invoice #{$this->invoice->invoiceNumber} Archived Notification")
            ->greeting("Hello {$this->playerName},")
            ->line("Your invoice #{$this->invoice->invoiceNumber} has been archived by our team.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: ".convertToDatetime($this->invoice->dueDate))
            ->action('View Payment Details', route('billing-and-payments.show', $this->invoice->id))
            ->line('If you have any questions, please feel free to contact us.')
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
            'data' =>'Your invoice #'.$this->invoice->invoiceNumber.' has been archived.',
            'redirectRoute' => route('billing-and-payments.show', ['invoice' => $this->invoice->id])
        ];
    }
}
