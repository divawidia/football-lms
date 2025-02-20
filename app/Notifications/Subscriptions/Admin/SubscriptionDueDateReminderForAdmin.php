<?php

namespace App\Notifications\Subscriptions\Admin;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionDueDateReminderForAdmin extends Notification implements ShouldQueue
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
            ->subject("Player Subscription Due Date Reminder")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName},")
            ->line("This is a reminder that a player academy subscription is due soon.")
            ->line('Player: ' .getUserFullName($this->subscription->user))
            ->line('Due Date: ' . convertToDatetime($this->subscription->nextDueDate))
            ->action('View Subscription', route('subscriptions.show', ['subscription' => $this->subscription->hash]))
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
            'title' => "Player Subscription Due Date Reminder",
            'data' => 'A player '.getUserFullName($this->subscription->user).' academy subscription for '.$this->subscription->product->productName.' is due soon at ' . convertToDatetime($this->subscription->nextDueDate) . '. Please review.',
            'redirectRoute' => route('subscriptions.show', ['subscription' => $this->subscription->hash])
        ];
    }
}
