<?php

namespace App\Notifications\TeamsManagements\Admin;

use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamUpdatedNotification extends Notification
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
            'title' => "Team Has been updated",
            'data' =>"Admin {$this->loggedUser->firstName} {$this->loggedUser->lastName} has updated a team {$this->team->teamName}. Please review the changes if necessary!",
            'redirectRoute' => route('team-managements.show', $this->team->hash)
        ];
    }
}
