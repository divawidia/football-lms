<?php

namespace App\Notifications\PlayerManagements\Admin;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\Player;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PlayerCreatedForAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected User $loggedUser;
    protected Player $player;

    public function __construct(User $loggedUser, Player $player)
    {
        $this->loggedUser= $loggedUser;
        $this->player = $player;
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
            'title' => "New player Account created",
            'data' => "Admin {$this->loggedUser->firstName} {$this->loggedUser->lastName} has created a new player {$this->player->user->firstName} {$this->player->user->lastName}. Please review the changes if necessary!",
            'redirectRoute' => route('player-managements.show', $this->player->hash),
        ];
    }
}
