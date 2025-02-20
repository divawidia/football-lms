<?php

namespace App\Notifications\Subscriptions\Player;

use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionPastDueForPlayer extends Notification implements ShouldQueue
{
    use Queueable;
    protected Invoice $invoice;
    protected Subscription $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice, Subscription $subscription)
    {
        $this->invoice = $invoice;
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
            ->subject("Academy Subscription Is Past Due")
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
            'title' => "Academy Subscription Is Past Due",
            'data' => '⚠️ Reminder: Your subscription payment of '.$this->subscription->product->productName.' is past due on '.convertToDatetime($this->invoice->dueDate).'. Please contact our admin to continue your subscription in academy!',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
