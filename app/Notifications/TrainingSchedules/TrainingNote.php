<?php

namespace App\Notifications\TrainingSchedules;

use App\Models\Coach;
use App\Models\EventSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingNote extends Notification
{
    use Queueable;
    protected Coach $coach;
    protected EventSchedule $trainingSession;
    protected string $action; // Either 'created', 'updated' or 'deleted'

    /**
     * Create a new notification instance.
     *
     * @param $coach
     * @param $trainingSession
     * @param string $action
     */
    public function __construct($coach, $trainingSession, $action)
    {
        $this->coach = $coach;
        $this->trainingSession = $trainingSession;
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

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' =>'Coach '.$this->coach->user->firstName.' '.$this->coach->user->lastName.' has '.$this->action.' a note for '.$this->trainingSession->teams[0]->teamName.' training session '.$this->trainingSession->eventName.'. Please check the note if needed!',
            'redirectRoute' => route('training-schedules.show', $this->trainingSession->id)
        ];
    }
}
