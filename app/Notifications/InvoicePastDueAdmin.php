<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePastDueAdmin extends Notification
{
    use Queueable;
    protected $duedate;
    protected $invoiceId;
    protected $invoiceNumber;
    protected $playerName;
    /**
     * Create a new notification instance.
     */
    public function __construct($duedate, $invoiceId, $invoiceNumber, $playerName)
    {
        $this->duedate = $duedate;
        $this->invoiceId = $invoiceId;
        $this->invoiceNumber = $invoiceNumber;
        $this->playerName = $playerName;
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
            'data' =>'Player '.$this->playerName.' has an overdue invoice #'.$this->invoiceNumber.' is overdue at '.$this->duedate.'. Consider following up with them to complete the payment as soon as possible.',
            'redirectRoute' => route('invoices.show', ['invoice' => $this->invoiceId])
        ];
    }
}