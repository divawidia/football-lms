<?php

namespace App\Notifications\TeamsManagements\Admin;

use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AddCoachToTeamForAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected User $loggedUser;
    protected Team $team;

    public function __construct(User $loggedUser, Team $team)
    {
        $this->loggedUser = $loggedUser;
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
            'title' => "New coaches/staffs has been added to team",
            'data' => "Admin {$this->loggedUser->firstName} {$this->loggedUser->lastName} has added new coaches/staffs to the {$this->team->teamName}. Please review the change if necessary!",
            'redirectRoute' => route('team-managements.show', $this->team->hash)
        ];
    }
}
