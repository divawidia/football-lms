<?php

namespace App\Notifications\SkillAssessment;

use App\Models\Coach;
use App\Models\MatchModel;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlayerAssessedInMatchNotification extends Notification
{
    use Queueable;
    protected Coach $coach;
    protected MatchModel $match;

    public function __construct(Coach $coach, MatchModel $match)
    {
        $this->coach = $coach;
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
        if ($this->match->matchType == 'Internal Match') {
            return $this->match->homeTeam->teamName. " Vs. ". $this->match->awayTeam->teamName;
        } else {
            return $this->match->homeTeam->teamName. " Vs. ". $this->match->externalTeam->teamName;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Skill Stats Assessed",
            'data' => 'Your skills have been assessed by coach '.$this->coach->user->firstName.' '.$this->coach->user->lastName.' in the '.$this->matchTeams().' match session.',
            'redirectRoute' => route('skill-stats')
        ];
    }
}
