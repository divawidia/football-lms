<?php

namespace App\Notifications\PerformanceReview;

use App\Models\Coach;
use App\Models\MatchModel;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewCreatedInMatchNotification extends Notification
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
            'title' => "Performance Review Created",
            'data' => "'Your performance review in the {$this->matchTeams()} match session have been created by coach {$this->coach->user->firstName} {$this->coach->user->lastName}.",
            'redirectRoute' => route('match-schedules.show', $this->match->hash)
        ];
    }
}
