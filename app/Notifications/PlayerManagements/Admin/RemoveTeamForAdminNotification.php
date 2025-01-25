<?php

namespace App\Notifications\PlayerManagements\Admin;

use App\Models\Coach;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RemoveTeamForAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected User $user;
    protected Team $team;
    protected Player $player;

    public function __construct(User $user, Team $team, Player $player)
    {
        $this->user = $user;
        $this->team = $team;
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
            'title' => "player have been removed from {$this->team->teamName}",
            'data' => "Admin {$this->user->firstName} {$this->user->lastName} has been removed player {$this->player->user->firstName} {$this->player->user->lastName} from {$this->team->teamName}",
            'redirectRoute' => route('team-managements.show', $this->team->hash)
        ];
    }
}
