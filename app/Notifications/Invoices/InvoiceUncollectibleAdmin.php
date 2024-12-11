<?php

namespace App\Notifications\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceUncollectibleAdmin extends Notification implements ShouldQueue
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
            ->subject("Invoice #{$this->invoice->invoiceNumber} Marked as Uncollectible for {$this->playerName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The following player’s invoice has been marked as uncollectible.")
            ->line("Player : {$this->playerName}")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Original Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Status: Uncollectible")
            ->line('You can review the invoice details and take any necessary actions in the invoices page.')
            ->action('View Invoice Details', url()->route('invoices.show', $this->invoice->id))
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
            'data' =>'The invoice #'.$this->invoice->invoiceNumber.' for player '.$this->playerName.' has been marked as uncollectible. Click here to see the invoice!',
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoice->id])
        ];
    }
}
