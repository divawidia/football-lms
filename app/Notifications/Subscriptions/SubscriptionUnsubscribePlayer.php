<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionUnsubscribePlayer extends Notification
{
    use Queueable;
    protected $productName;

    /**
     * Create a new notification instance.
     */
    public function __construct($productName)
    {
        $this->productName = $productName;
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
            ->subject("Your Subscription for {$this->productName} Has Ended")
            ->greeting("Hello {$this->productName}, we wanted to let you know that your subscription for {$this->productName} has ended.")
            ->line('To continue accessing training resources, facilities, and sessions, please renew your subscription. Reach out if you need assistance with the renewal process!')
            ->action('View Subscription at', url()->route('billing-and-payments.index'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => '⛔ Subscription Ended: Your subscription for '.$this->productName.' has expired, and your access to our resources has been paused. Renew now to regain access to all training materials and sessions.',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
