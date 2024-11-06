<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCreated extends Notification
{
    use Queueable;
    protected $productName;
    protected $playerName;
    protected $invoiceNumber;
    protected $invoiceId;

    /**
     * Create a new notification instance.
     */
    public function __construct($productName, $playerName, $invoiceId, $invoiceNumber)
    {
        $this->productName = $productName;
        $this->playerName = $playerName;
        $this->invoiceId = $invoiceId;
        $this->invoiceNumber = $invoiceNumber;
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
            ->subject('Subscription Activated')
            ->greeting("Hello, {$this->playerName}!")
            ->line('Your subscription has been activated successfully.')
            ->line("Invoice Number: {$this->invoiceNumber}")
            ->action('View Invoice', url('/billing-and-payments/' . $this->invoiceId))
            ->line('Thank you for joining the academy!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' =>'Subscription of '.$this->productName.' for player '.$this->playerName.' has been activated.',
            'redirectRoute' => '#'
        ];
    }
}
