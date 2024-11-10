<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCreatedAdmin extends Notification
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
            ->subject("{$this->subscription->product->productName} subscription for player {$this->playerName} has been created")
            ->greeting("Dear, admins!")
            ->line("Subscription of {$this->subscription->product->productName} for player {$this->playerName} has been successfully created.")
            ->action('View Subscription detail at', url()->route('subscriptions.show', $this->subscription->id))
            ->line("Please follow up the player {$this->playerName} to complete the payment of invoice #{$this->invoice->invoiceNumber} as soon as possible to activate the subscription")
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
            'data' =>'Subscription of '.$this->subscription->product->productName.' has been created. Please follow up the player '.$this->playerName.' to pay the invoice #'.$this->invoice->invoiceNumber.' as soon as possible to activate your subscription status!',
            'redirectRoute' => route('subscriptions.show', $this->subscription->id)
        ];
    }
}
