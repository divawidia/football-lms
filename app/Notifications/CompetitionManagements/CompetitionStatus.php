<?php

namespace App\Notifications\CompetitionManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompetitionStatus extends Notification
{
    use Queueable;
    protected $competition;
    protected $team;
    protected $status;

    public function __construct($competition, $team, $status)
    {
        $this->competition = $competition;
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
            'data' =>"Competition {$this->competition->name} has started, and your team '{$this->team->teamName}' {$this->status}.",
            'redirectRoute' => route('competition-managements.show', $this->competition->id)
        ];
    }
}
