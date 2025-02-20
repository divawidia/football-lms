<?php

namespace App\Notifications\Subscriptions\Player;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionScheduledForPlayer extends Notification implements ShouldQueue
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
            ->subject("Academy Subscription Activated")
            ->greeting("Hello, {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("We are pleased to inform you that your subscription invoice for {$this->subscription->product->productName} has been successfully paid.")
            ->line('Your subscription is now active.')
            ->action('View Subscription', route('billing-and-payments.index'))
            ->line('Thank you for choosing our Football Academy!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' =>"Academy Subscription Activated/Scheduled",
            'data' => 'Your subscription for '.$this->subscription->product->productName.' has been scheduled.',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
