<?php

namespace App\Notifications\Subscriptions\Player;

use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCreatedForPlayer extends Notification implements ShouldQueue
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
            ->subject("Your subscription has been created")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("Your subscription for {$this->subscription->product->productName} has been successfully created.")
            ->action('View Subscription at', route('billing-and-payments.index'))
            ->line("Please pay your invoice #{$this->invoice->invoiceNumber} as soon as possible to activate your subscription")
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
            'title' => "A New subscription has been created",
            'data' =>'Your Subscription of '.$this->subscription->product->productName.' has been created. Please pay your invoice #'.$this->invoice->invoiceNumber.' as soon as possible to activate your subscription status!',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
