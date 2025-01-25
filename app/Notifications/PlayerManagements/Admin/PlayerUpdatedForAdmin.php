<?php

namespace App\Notifications\PlayerManagements\Admin;

use App\Models\Player;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PlayerUpdatedForAdmin extends Notification implements ShouldQueue
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
            'title' => "Player account has been updated",
            'data' => "Admin {$this->admin->firstName} {$this->admin->lastName} has been updated player {$this->player->user->firstName} {$this->player->user->lastName} account. Please review the changes if necessary!",
            'redirectRoute' => route('player-managements.show', $this->player->hash)
        ];
    }
}
