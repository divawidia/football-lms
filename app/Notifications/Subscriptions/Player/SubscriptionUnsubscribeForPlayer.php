<?php

namespace App\Notifications\Subscriptions\Player;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionUnsubscribeForPlayer extends Notification implements ShouldQueue
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
            ->subject("Academy Subscription Has Been Ended")
            ->greeting("Hello, {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("We wanted to let you know that your Academy subscription for {$this->subscription->product->productName} has been ended.")
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
            'title' => "Academy Subscription Has Been Ended",
            'data' => 'Your subscription for '.$this->subscription->product->productName.' has expired, and your access to our resources has been paused. Renew now to regain access to all training materials and sessions.',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
