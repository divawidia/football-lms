<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiredAdmin extends Notification
{
    use Queueable;
    protected $productName;
    protected $playerName;
    protected $invoiceNumber;
    protected $dueDate;
    protected $subscriptionId;

    /**
     * Create a new notification instance.
     */
    public function __construct($productName, $playerName, $invoiceNumber, $dueDate, $subscriptionId)
    {
        $this->productName = $productName;
        $this->playerName = $playerName;
        $this->invoiceNumber = $invoiceNumber;
        $this->dueDate = $dueDate;
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Player {$this->productName} Subscription Renewal Reminder")
            ->greeting("Dear Admin, the subscription for {$this->playerName} will be due on {$this->dueDate}!")
            ->line('Please review and follow up if necessary to assist the player in completing their subscription of '.$this->productName.' renewal')
            ->action('View Subscription at', url()->route('subscriptions.show', $this->subscriptionId));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
     {
        return [
            'message' => '📅 Reminder: The subscription of '.$this->productName.' for player '.$this->playerName.' is due on '.$this->dueDate.'. Ensure the player completes the renewal to maintain active status!',
            'redirectRoute' => route('subscriptions.show', $this->subscriptionId)
        ];
    }
}
