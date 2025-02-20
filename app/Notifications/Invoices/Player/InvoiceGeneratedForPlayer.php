<?php

namespace App\Notifications\Invoices\Player;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedForPlayer extends Notification implements ShouldQueue
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
            ->subject("Your New Invoice #{$this->invoice->invoiceNumber} is Ready")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("A new invoice #{$this->invoice->invoiceNumber} has been created for your payments.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: ".convertToDatetime($this->invoice->dueDate))
            ->action('View Invoice here', route('billing-and-payments.show', ['invoice' => $this->invoice->hash]))
            ->line("Please ensure payment is completed as soon as possible by the due date.")
            ->line("If you have any questions, feel free to reach out to our support team.")
            ->line("Thank you!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "New Invoice has been sent to you",
            'data' =>'A new invoice '.$this->invoice->invoiceNumber.' has been sent for you. Please pay your invoice before due date '.convertToDatetime($this->invoice->dueDate).' as soon as possible!',
            'redirectRoute' => route('billing-and-payments.show', ['invoice' => $this->invoice->hash])
        ];
    }
}
