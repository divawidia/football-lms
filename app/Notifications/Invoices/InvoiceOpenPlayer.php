<?php

namespace App\Notifications\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceOpenPlayer extends Notification implements ShouldQueue
{
    use Queueable;
    protected $dueDate;
    protected $invoiceId;
    protected $invoiceNumber;
    /**
     * Create a new notification instance.
     */
    public function __construct($dueDate, $invoiceId, $invoiceNumber)
    {
        $this->dueDate = $dueDate;
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
            'data' =>'Your invoice #'.$this->invoiceNumber.' has been regenerated, please complete the payment before the due date at '.$this->dueDate.'!',
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoiceId])
        ];
    }
}
