<?php

namespace App\Notifications\CoachManagements\Admin;

use App\Models\Coach;
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
    protected Coach $coach;

    public function __construct(User $user, Team $team, Coach $coach)
    {
        $this->user = $user;
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
            'title' => "Coach have been removed from {$this->team->teamName}",
            'data' => "Admin {$this->user->firstName} {$this->user->lastName} has been removed coach {$this->coach->user->firstName} {$this->coach->user->lastName} from {$this->team->teamName}",
            'redirectRoute' => route('team-managements.show', $this->team->hash)
        ];
    }
}
