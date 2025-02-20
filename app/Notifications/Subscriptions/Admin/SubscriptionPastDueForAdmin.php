<?php

namespace App\Notifications\Subscriptions\Admin;

use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionPastDueForAdmin extends Notification implements ShouldQueue
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
            ->subject("Player Subscription Past Due")
            ->greeting("Hello, {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("The subscription payment of {$this->subscription->product->productName} for player : ".getUserFullName($this->subscription->user)." is past due on ".convertToDatetime($this->invoice->dueDate)."!")
            ->action('View Subscription at', route('subscriptions.show', $this->subscription->hash))
            ->line('Please review and follow up if necessary to assist the player in completing their subscription of '.$this->subscription->product->productName.' renewal');

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
     {
        return [
            'title' => "Player Subscription Past Due",
            'data' => '📅 Reminder: The subscription of '.$this->subscription->product->productName.' for player : '.getUserFullName($this->subscription->user).' is past due on '.convertToDatetime($this->invoice->dueDate).'. Ensure the player completes the renewal to maintain active status!',
            'redirectRoute' => route('subscriptions.show', $this->subscription->hash)
        ];
    }
}
