<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGenerated extends Notification
{
    use Queueable;
    protected $amount;
    protected $duedate;
    protected $invoiceId;

    /**
     * Create a new notification instance.
     */
    public function __construct($amount, $duedate, $invoiceId)
    {
        $this->amount = $amount;
        $this->duedate = $duedate;
        $this->invoiceId = $invoiceId;
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
            'data' =>'An invoice of Rp. '. number_format($this->amount).' has been generated. Please complete the payment before the due date at '.$this->duedate.'!',
            'redirectRoute' => route('billing-and-payments.show', ['invoice' => $this->invoiceId])
        ];
    }
}
