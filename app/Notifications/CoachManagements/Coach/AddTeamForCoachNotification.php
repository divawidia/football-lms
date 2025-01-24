<?php

namespace App\Notifications\CoachManagements\Coach;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AddTeamForCoachNotification extends Notification implements ShouldQueue
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
            'title' => "You have been added to {$this->team->teamName}",
            'data' => "Admin has been added you to the {$this->team->teamName}",
            'redirectRoute' => route('team-managements.show', $this->team->hash)
        ];
    }
}
