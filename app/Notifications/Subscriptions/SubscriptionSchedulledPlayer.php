<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionSchedulledPlayer extends Notification implements ShouldQueue
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
            ->subject("Subscription for {$this->subscription->product->productName} Activated")
            ->greeting('Hello, ' . $this->playerName)
            ->line("We are pleased to inform you that your subscription invoice for {$this->subscription->product->productName} has been successfully paid.")
            ->line('Your subscription is now active and ready to use.')
            ->action('View Subscription', route('billing-and-payments'))
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
            'data' => 'Your subscription for '.$this->subscription->product->productName.' has been scheduled.',
            'redirectRoute' => route('billing-and-payments')
        ];
    }
}
