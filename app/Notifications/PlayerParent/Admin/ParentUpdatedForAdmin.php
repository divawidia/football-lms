<?php

namespace App\Notifications\PlayerParent\Admin;

use App\Models\Player;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ParentUpdatedForAdmin extends Notification implements ShouldQueue
{
    use Queueable;
    protected User $admin;
    protected Player $player;

    public function __construct(User $admin, Player $player)
    {
        $this->admin = $admin;
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
            'title' => "Player parent/guardian has been updated",
            'data' => "Admin {$this->admin->firstName} {$this->admin->lastName} has been updated parent/guardian of player {$this->player->user->firstName} {$this->player->user->lastName}. Please review the changes if necessary!",
            'redirectRoute' => route('player-managements.show', $this->player->hash)
        ];
    }
}
