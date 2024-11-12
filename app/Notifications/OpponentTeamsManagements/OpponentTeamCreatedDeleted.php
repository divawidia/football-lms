<?php

namespace App\Notifications\OpponentTeamsManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OpponentTeamCreatedDeleted extends Notification
{
    use Queueable;
    protected $adminName;
    protected $team;
    protected $status;

    public function __construct($adminName, $team, $status)
    {
        $this->adminName = $adminName;
        $this->team = $team;
        $this->status = $status;
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
            'data' =>"{$this->adminName} has {$this->status} a new opponent team {$this->team->teamName}. Please review the changes if necessary.",
            'redirectRoute' => route('opponentTeam-managements.show', $this->team->id)
        ];
    }
}
