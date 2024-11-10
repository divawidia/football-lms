<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiredPlayer extends Notification
{
    use Queueable;
    protected $productName;
    protected $playerName;
    protected $invoiceNumber;
    protected $dueDate;

    /**
     * Create a new notification instance.
     */
    public function __construct($productName, $playerName, $invoiceNumber, $dueDate)
    {
        $this->productName = $productName;
        $this->playerName = $playerName;
        $this->invoiceNumber = $invoiceNumber;
        $this->dueDate = $dueDate;
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
            ->subject("Upcoming Subscription Renewal for {$this->productName}")
            ->greeting("Hello, {$this->playerName}!")
            ->line('Just a quick reminder that your subscription of '.$this->productName.' will be due on '.$this->dueDate.'.')
            ->line('Please ensure that your payment is completed to continue enjoying uninterrupted access to all our facilities and training resources')
            ->action('View Subscription at', route('billing-and-payments.index'))
            ->line('Please pay your invoice #'.$this->invoiceNumber.' to continue your subscription')
            ->line('Keep up the great work in the academy!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => '⚠️ Reminder: Your subscription of '.$this->productName.' is due on '.$this->dueDate.'. Ensure you renew on time to keep your access to exclusive training resources and sessions!',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
