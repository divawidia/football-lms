<?php

namespace App\Notifications\PlayerManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayerParent extends Notification
{
    use Queueable;
    protected $parent;
    protected $status;

    public function __construct($parent, $status)
    {
        $this->parent = $parent;
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
            'data' =>"Admin have been {$this->status} your parent {$this->parent->firstName} {$this->parent->lastName} data. Please review the changes if necessary.",
            'redirectRoute' => route('player.dashboard')
        ];
    }
}
