<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCreatedAdmin extends Notification
{
    use Queueable;
    protected $productName;
    protected $playerName;
    protected $invoiceNumber;
    protected $subscriptionId;

    /**
     * Create a new notification instance.
     */
    public function __construct($productName, $playerName, $invoiceNumber, $subscriptionId)
    {
        $this->productName = $productName;
        $this->playerName = $playerName;
        $this->invoiceNumber = $invoiceNumber;
        $this->subscriptionId = $subscriptionId;
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
            ->subject("{$this->productName} subscription for player {$this->playerName} has been created")
            ->greeting("Dear, admins!")
            ->line("Subscription of {$this->productName} for player {$this->playerName} has been successfully created.")
            ->action('View Subscription detail at', url()->route('subscriptions.show', $this->subscriptionId))
            ->line("Please follow up the player {$this->playerName} to complete the payment of invoice #{$this->invoiceNumber} as soon as possible to activate the subscription")
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
            'data' =>'Your Subscription of '.$this->productName.' has been created. Please pay your invoice #'.$this->invoiceNumber.' as soon as possible to activate your subscription status!',
            'redirectRoute' => route('subscriptions.show', $this->subscriptionId)
        ];
    }
}
