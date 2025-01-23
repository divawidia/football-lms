<?php

namespace App\Notifications\MatchSchedules;

use App\Models\MatchModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MatchNote extends Notification implements ShouldQueue
{
    use Queueable;
    protected MatchModel $match;
    protected string $action; // Either 'created', 'updated' or 'deleted'

    public function __construct($match, $action)
    {
        $this->match = $match;
        $this->action = $action;
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

    private function matchTeams()
    {
        return ($this->match->matchType == 'Internal Match') ? $this->match->homeTeam->teamName. " Vs. ". $this->match->awayTeam->teamName
         : $this->match->homeTeam->teamName. " Vs. ". $this->match->externalTeam->teamName;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => 'A note for '.$this->matchTeams().' match session has been '.$this->action.'. Please check the note if needed!',
            'redirectRoute' => route('match-schedules.show', $this->match->hash)
        ];
    }
}
