<?php

namespace App\Notifications\TeamsManagements\Admin;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RemovePlayerFromTeamForAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected User $loggedUser;
    protected Team $team;
    protected Player $player;

    public function __construct(User $loggedUser, Team $team ,Player $player)
    {
        $this->loggedUser = $loggedUser;
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
            'title' => "Player has been removed from team",
            'data' => "Admin {$this->loggedUser->firstName} {$this->loggedUser->lastName} has removed player {$this->player->user->firstName} {$this->player->user->lastName} from the {$this->team->teamName}. Please review the change if necessary!",
            'redirectRoute' => route('team-managements.show', $this->team->hash)
        ];
    }
}
