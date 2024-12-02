<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionUnsubscribePlayer extends Notification
{
    use Queueable;
    protected $subscription;
    protected $playerName;

    /**
     * Create a new notification instance.
     */
    public function __construct($subscription, $playerName)
    {
        $this->subscription = $subscription;
        $this->playerName = $playerName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your Subscription for {$this->subscription->product->productName} Has Ended")
            ->greeting("Hello {$this->playerName},")
            ->line("We wanted to let you know that your subscription for {$this->subscription->product->productName} has ended.")
            ->line('To continue accessing training resources, facilities, and sessions, please renew your subscription. Reach out if you need assistance with the renewal process!')
            ->action('View Subscription at', route('billing-and-payments.index'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => '⛔ Subscription Ended: Your subscription for '.$this->subscription->product->productName.' has expired, and your access to our resources has been paused. Renew now to regain access to all training materials and sessions.',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
