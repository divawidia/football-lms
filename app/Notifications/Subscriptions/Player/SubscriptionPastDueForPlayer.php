<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionPastDuePlayer extends Notification implements ShouldQueue
{
    use Queueable;
    protected $subscription;
    protected $invoice;
    /**
     * Create a new notification instance.
     */
    public function __construct($subscription, $invoice)
    {
        $this->subscription = $subscription;
        $this->invoice = $invoice;
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
            ->subject("Upcoming Subscription Renewal for {$this->subscription->product->productName}")
            ->greeting("Hello, {$notifiable->firstName} {$notifiable->lastName}!")
            ->line('Your subscription payment of '.$this->subscription->product->productName.' is past due on '.convertToDatetime($this->invoice->dueDate).'.')
            ->action('View Subscription at', route('billing-and-payments.index'))
            ->line('Please contact our admin to continue your subscription in academy')
            ->line('Keep up the great work in the academy!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => '⚠️ Reminder: Your subscription payment of '.$this->subscription->product->productName.' is past due on '.convertToDatetime($this->invoice->dueDate).'. Please contact our admin to continue your subscription in academys!',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
