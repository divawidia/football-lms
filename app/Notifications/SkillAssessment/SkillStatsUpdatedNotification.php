<?php

namespace App\Notifications\SkillAssessment;

use App\Models\Coach;
use App\Models\MatchModel;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SkillStatsUpdatedNotification extends Notification
{
    use Queueable;
    protected Coach $coach;

    public function __construct(Coach $coach)
    {
        $this->coach = $coach;
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
            'title' => "Skill Stats Updated",
            'data' => 'Your skills have been updated by coach '.$this->coach->user->firstName.' '.$this->coach->user->lastName.'.',
            'redirectRoute' => route('skill-stats')
        ];
    }
}
