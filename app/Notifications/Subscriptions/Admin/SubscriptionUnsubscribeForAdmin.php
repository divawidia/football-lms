<?php

namespace App\Notifications\Subscriptions\Admin;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionUnsubscribeForAdmin extends Notification implements ShouldQueue
{
    use Queueable;
    protected Subscription $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'mail',
            'database'
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Player Subscription Lapsed")
            ->greeting("Hello, {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("The player : ".getUserFullName($this->subscription->user)." subscription for {$this->subscription->product->productName} has ended.")
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
            'title' => "Player Subscription Lapsed",
            'data' => 'The player : '.getUserFullName($this->subscription->user).' subscriptions for '.$this->subscription->product->productName.' has ended. They no longer have access to training resources and sessions. Consider following up if they may be interested in renewing.',
            'redirectRoute' => route('subscriptions.show', $this->subscription->hash)
        ];
    }
}
