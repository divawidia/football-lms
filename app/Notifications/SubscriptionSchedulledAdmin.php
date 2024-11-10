<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionSchedulledAdmin extends Notification
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
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Player {$this->playerName} Subscription for {$this->subscription->product->productName} Activated/Scheduled")
            ->greeting('Hello, Admins')
            ->line("A player {$this->playerName} has successfully paid their subscription invoice for {$this->subscription->product->productName}, and the subscription is now active or scheduled.")
            ->line('Player: ' . $this->playerName)
            ->line('Subscription ID: ' . $this->subscription->id)
            ->line('Your subscription is now active and scheduled.')
            ->action('View Player Subscription', route('subscriptions.show', ['subscription' => $this->subscription->id]))
            ->line('Please monitor the player’s progress.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => 'Player ' . $this->playerName . ' has successfully paid their subscription invoice for '.$this->subscription->product->productName.', and the subscription is active.',
            'redirectRoute' => route('subscriptions.show', ['subscription' => $this->subscription->id])
        ];
    }
}
