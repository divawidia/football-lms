<?php

namespace App\Notifications\TeamsManagements\PlayerCoach;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RemoveFromTeamForPlayerCoachNotification extends Notification
{
    use Queueable;
    protected Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
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
            'title' => "Removed from Team",
            'data' => "Admin has been removed you from the {$this->team->teamName}. Please review the change if necessary!",
            'redirectRoute' => route('team-managements.index')
        ];
    }
}
