<?php

namespace App\Notifications\Subscriptions\Admin;

use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionSchedulledForAdmin extends Notification implements ShouldQueue
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
            ->subject("Player Subscription Activated/Scheduled")
            ->greeting("Hello, {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("Player : ".getUserFullName($this->subscription->user)." has successfully paid their subscription invoice for {$this->subscription->product->productName}, and the subscription is now active or scheduled.")
            ->action('View Player Subscription', route('subscriptions.show', ['subscription' => $this->subscription->hash]))
            ->line("If you have any questions or require further information, please don't hesitate to reach out.!");
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
            'redirectRoute' => route('subscriptions.show', ['subscription' => $this->subscription->hash])
        ];
    }
}
