<?php

namespace App\Notifications\Subscriptions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewedAdmin extends Notification implements ShouldQueue
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
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("{$this->productName} subscription renewed")
            ->greeting("Hello, {$this->playerName}!")
            ->line('Your subscription has been successfully created.')
            ->line("Subscription : {$this->productName}")
            ->action('View Subscription at', route('billing-and-payments.index'))
            ->line('Please pay your invoice #'.$this->invoiceNumber.' to activate your subscription')
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
            'data' => 'Subscription of '.$this->productName.' for player '.$this->playerName.' has been renewed.',
            'redirectRoute' => route('billing-and-payments.show', $this->subscriptionId)
        ];
    }
}
