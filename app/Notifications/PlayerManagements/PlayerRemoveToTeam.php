<?php

namespace App\Notifications\PlayerManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayerRemoveToTeam extends Notification
{
    use Queueable;
    protected $team;

    public function __construct($team)
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
            'data' =>"Admin has removed you from the {$this->team->teamName} team.",
            'redirectRoute' => route('team-managements.show', $this->team->id)
        ];
    }
}
