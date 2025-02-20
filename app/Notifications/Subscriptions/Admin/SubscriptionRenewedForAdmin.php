<?php

namespace App\Notifications\Subscriptions\Admin;

use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewedForAdmin extends Notification implements ShouldQueue
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
            ->subject("Player subscription renewed")
            ->greeting("Hello, {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("{$this->subscription->product->productName} subscription for player : ".getUserFullName($this->subscription->user)." has been successfully renewed.")
            ->action('View Subscription detail at', route('subscriptions.show', $this->subscription->hash))
            ->line("If you have any questions or require further information, please don't hesitate to reach out.!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' =>"Player subscription renewed",
            'data' => 'Subscription of '.$this->subscription->product->productName.' for player : '.getUserFullName($this->subscription->user).' has been renewed.',
            'redirectRoute' => route('subscriptions.show', $this->subscription->hash)
        ];
    }
}
