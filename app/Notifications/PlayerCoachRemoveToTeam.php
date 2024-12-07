<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PlayerCoachRemoveToTeam extends Notification implements ShouldQueue
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
