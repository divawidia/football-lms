<?php

namespace App\Notifications\TeamsManagements\Admin;

use App\Models\Coach;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RemoveCoachFromTeamForAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected User $loggedUser;
    protected Team $team;
    protected Coach $coach;

    public function __construct(User $loggedUser, Team $team ,Coach $coach)
    {
        $this->loggedUser = $loggedUser;
        $this->team = $team;
        $this->coach = $coach;
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
            'title' => "Coach/staff has been removed from team",
            'data' => "Admin {$this->loggedUser->firstName} {$this->loggedUser->lastName} has removed coach/staff {$this->coach->user->firstName} {$this->coach->user->lastName} from the {$this->team->teamName}. Please review the change if necessary!",
            'redirectRoute' => route('team-managements.show', $this->team->hash)
        ];
    }
}
