<?php

namespace App\Notifications\MatchSchedules;

use App\Models\Coach;
use App\Models\MatchModel;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchNoteDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected MatchModel $match;

    public function __construct(MatchModel $match)
    {
        $this->match = $match;
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
            'title' => "Match session Note deleted",
            'data' => "'A note for {$this->matchTeams()} match session has been deleted. Please check the note if needed!'",
            'redirectRoute' => route('match-schedules.show', $this->match->hash)
        ];
    }
}
