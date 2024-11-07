<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionUnsubscribeAdmin extends Notification
{
    use Queueable;
    protected $productName;
    protected $playerName;
    protected $subscriptionId;

    /**
     * Create a new notification instance.
     */
    public function __construct($productName, $playerName, $subscriptionId)
    {
        $this->productName = $productName;
        $this->playerName = $playerName;
        $this->subscriptionId = $subscriptionId;
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
            ->subject("Player Subscription Lapsed: {$this->playerName}")
            ->greeting("Dear, admin!")
            ->line("The {$this->productName} subscription for {$this->playerName} has ended.")
            ->line("The player no longer has access to our resources and sessions")
            ->action('View Subscription at', url()->route('subscriptions.show', $this->subscriptionId))
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
            'message' => '⚠️ Alert: The '.$this->productName.' subscription for '.$this->playerName.' has ended. They no longer have access to training resources and sessions. Consider following up if they may be interested in renewing.',
            'redirectRoute' => route('subscriptions.show', $this->subscriptionId)
        ];
    }
}
