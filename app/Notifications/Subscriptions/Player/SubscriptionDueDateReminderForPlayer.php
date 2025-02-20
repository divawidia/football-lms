<?php

namespace App\Notifications\Subscriptions\Player;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionDueDateReminderForPlayer extends Notification implements ShouldQueue
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
            ->subject("Subscription Due Date Reminder")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName},")
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
            'title' => "Academy Subscription Due Date Reminder",
            'data' => 'Your academy subscription is due on ' . convertToDatetime($this->subscription->nextDueDate) . '. Please ensure to pay your subscription fee after the invoice sent to you.',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
