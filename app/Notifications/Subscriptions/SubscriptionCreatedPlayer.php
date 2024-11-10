<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCreatedPlayer extends Notification
{
    use Queueable;
    protected $invoice;
    protected $subscription;
    protected $playerName;

    /**
     * Create a new notification instance.
     */
    public function __construct($invoice, $subscription, $playerName)
    {
        $this->invoice = $invoice;
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your {$this->subscription->product->productName} subscription created")
            ->greeting("Hello, {$this->playerName}!")
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
            'data' =>'Your Subscription of '.$this->subscription->product->productName.' has been created. Please pay your invoice #'.$this->invoice->invoiceNumber.' as soon as possible to activate your subscription status!',
            'redirectRoute' => route('billing-and-payments.index')
        ];
    }
}
