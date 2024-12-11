<?php

namespace App\Notifications\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedPlayer extends Notification implements ShouldQueue
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
//        return ['mail', 'database'];
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your New Invoice #{$this->invoice->invoiceNumber} is Ready")
            ->greeting("Hello {$this->playerName},")
            ->line("A new invoice #{$this->invoice->invoiceNumber} has been created for your product payments.")
            ->line("Invoice Number: {$this->invoice->invoiceNumber}")
            ->line("Amount Due: ".priceFormat($this->invoice->ammountDue))
            ->line("Due Date: ".convertToDatetime($this->invoice->dueDate))
            ->action('View Invoice here', route('billing-and-payments.show', ['invoice' => $this->invoice->id]))
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
            'data' =>'💼 A new invoice '.$this->invoice->invoiceNumber.' has been created for you. Please pay your invoice before due date '.convertToDatetime($this->invoice->dueDate).' as soon as possible!',
            'redirectRoute' => route('billing-and-payments.show', ['invoice' => $this->invoice->id])
        ];
    }
}
