<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionDueDateReminderAdmin extends Notification
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
            ->subject("Player {$this->playerName} Academy Subscription {$this->subscription->product->productName} Due Date Reminder")
            ->greeting("Hello Admins,")
            ->line("This is a reminder that a player academy subscription is due soon at.")
            ->line('Player: ' . $this->playerName)
            ->line('Due Date: ' . convertToDatetime($this->subscription->nextDueDate))
            ->action('View Subscription', route('subscriptions.show', ['subscription' => $this->subscription->id]))
            ->line('Please follow up with the player if needed.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => 'A player '.$this->playerName.' academy subscription for '.$this->subscription->product->productName.' is due soon at ' . convertToDatetime($this->subscription->nextDueDate) . '. Please review.',
            'redirectRoute' => route('subscriptions.show', ['subscription' => $this->subscription->id])
        ];
    }
}
