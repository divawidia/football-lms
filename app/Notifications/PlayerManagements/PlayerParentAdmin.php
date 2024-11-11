<?php

namespace App\Notifications\PlayerManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayerParentAdmin extends Notification
{
    use Queueable;
    protected $adminName;
    protected $player;
    protected $status;

    public function __construct($adminName, $player, $status)
    {
        $this->adminName = $adminName;
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
            'data' =>"{$this->adminName} has {$this->status} player parent of {$this->player->user->firstName} {$this->player->user->lastName}. Please review the changes if necessary.",
            'redirectRoute' => route('player-managements.show', $this->player->id)
        ];
    }
}
