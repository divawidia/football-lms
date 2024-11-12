<?php

namespace App\Notifications\CompetitionManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamJoinedCompetition extends Notification
{
    use Queueable;
    protected $team;
    protected $competition;

    /**
     * Create a new notification instance.
     *
     * @param  string  $team
     * @param  string  $competition
     * @return void
     */
    public function __construct($team, $competition)
    {
        $this->team = $team;
        $this->competition = $competition;
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
            'data' =>"Your team {$this->team->teamName} has joined the competition: {$this->competition->name}.",
            'redirectRoute' => route('competition-managements.show', $this->competition->id)
        ];
    }
}
