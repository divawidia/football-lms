<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionUnsubscribeAdmin extends Notification implements ShouldQueue
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
            ->subject("Player Subscription Lapsed: {$this->playerName}")
            ->greeting("Dear Admins,")
            ->line("The subscription for {$this->subscription->product->productName} has ended.")
            ->line("The player no longer has access to our resources and sessions")
            ->action('View Subscription at', route('subscriptions.show', $this->subscription->hash))
            ->line('Please follow up if necessary to assist with a renewal if the player wishes to continue.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => '⚠️ Alert: The player'.$this->playerName.' subscriptions for '.$this->subscription->product->productName.' has ended. They no longer have access to training resources and sessions. Consider following up if they may be interested in renewing.',
            'redirectRoute' => route('subscriptions.show', $this->subscription->hash)
        ];
    }
}
