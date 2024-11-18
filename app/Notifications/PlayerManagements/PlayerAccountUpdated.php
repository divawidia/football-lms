<?php

namespace App\Notifications\PlayerManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayerAccountUpdated extends Notification
{
    use Queueable;
    protected $player;
    protected $status;

    public function __construct($player, $status)
    {
        $this->player = $player;
        $this->status = $status;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' =>"Your account have been {$this->status} by Admin. Please review the changes if necessary.",
            'redirectRoute' => route('edit-account.edit')
        ];
    }
}
