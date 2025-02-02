<?php

namespace App\Notifications\SkillAssessment;

use App\Models\Coach;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlayerAssessedInTrainingNotification extends Notification
{
    use Queueable;
    protected Coach $coach;
    protected Training $training;

    /**
     * Create a new notification instance.
     *
     * @param $coach
     * @param $trainingSession
     * @param string $action
     */
    public function __construct(Coach $coach, Training $training)
    {
        $this->coach = $coach;
        $this->training = $training;
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
            'title' => "Skill Stats Assessed",
            'data' => 'Your skills have been assessd by coach '.$this->coach->user->firstName.' '.$this->coach->user->lastName.' in the '.$this->training->topic.' training session.',
            'redirectRoute' => route('player.skill-stats')
        ];
    }
}
