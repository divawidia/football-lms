<?php

namespace App\Notifications\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceOpenAdmin extends Notification implements ShouldQueue
{
    use Queueable;
    protected $adminName;
    protected $invoiceId;
    protected $invoiceNumber;
    /**
     * Create a new notification instance.
     */
    public function __construct($adminName, $invoiceId, $invoiceNumber)
    {
        $this->adminName = $adminName;
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
            'data' =>'Invoice #'.$this->invoiceNumber.' has been regenerated or set open to pay by '.$this->adminName,
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoiceId])
        ];
    }
}
