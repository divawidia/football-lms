<?php

namespace App\Notifications\Subscriptions\Admin;

use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCreatedForAdmin extends Notification implements ShouldQueue
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
            ->subject("A New subscription has been created")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("{$this->subscription->product->productName} subscription for player ".getUserFullName($this->subscription->user)." has been successfully created.")
            ->action('View Subscription detail at', route('subscriptions.show', $this->subscription->hash))
            ->line("Please follow up the player ".getUserFullName($this->subscription->user)." to complete the payment of invoice #{$this->invoice->invoiceNumber} as soon as possible to activate the subscription")
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
            'title' => "A New subscription has been created",
            'data' =>'Subscription of '.$this->subscription->product->productName.' has been created. Please follow up the player '.getUserFullName($this->invoice->recieverUser).' to pay the invoice #'.$this->invoice->invoiceNumber.' as soon as possible to activate your subscription status!',
            'redirectRoute' => route('subscriptions.show', $this->subscription->hash)
        ];
    }
}
