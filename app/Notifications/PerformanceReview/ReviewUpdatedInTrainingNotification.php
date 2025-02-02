<?php

namespace App\Notifications\PerformanceReview;

use App\Models\Coach;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewUpdatedInTrainingNotification extends Notification
{
    use Queueable;
    protected Coach $coach;
    protected Training $training;

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
            'title' => "Performance Review Updated",
            'data' => "'Your performance review in the {$this->training->topic} training session have been updated by coach {$this->coach->user->firstName} {$this->coach->user->lastName}.",
            'redirectRoute' => route('training-schedules.show', $this->training->hash)
        ];
    }
}
