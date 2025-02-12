<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionDueDateReminderPlayer extends Notification implements ShouldQueue
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
            ->subject("Academy Subscription for {$this->subscription->product->productName} Due Date Reminder")
            ->greeting("Hello, {$this->playerName}")
            ->line("This is a reminder that your academy subscription for {$this->subscription->product->productName} is due on ".convertToDatetime($this->subscription->nextDueDate))
            ->line('Please ensure to pay your subscription fee after the invoice sent to you.')
            ->action('View Subscription', route('billing-and-payments.index'))
            ->line('Thank you for being part of our football academy!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => 'Your academy subscription is due on ' . convertToDatetime($this->subscription->nextDueDate) . '. Please ensure to pay your subscription fee after the invoice sent to you.',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
