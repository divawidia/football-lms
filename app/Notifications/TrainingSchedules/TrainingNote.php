<?php

namespace App\Notifications\TrainingSchedules;

use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingNote extends Notification implements ShouldQueue
{
    use Queueable;
    protected User $user;
    protected EventSchedule $trainingSession;
    protected string $action; // Either 'created', 'updated' or 'deleted'

    public function __construct($user, $trainingSession, $action)
    {
        $this->user = $user;
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
            'data' => 'A note for '.$this->trainingSession->teams[0]->teamName.' training session '.$this->trainingSession->eventName.' has been '.$this->action.'. Please check the note if needed!',
            'redirectRoute' => route('training-schedules.show', $this->trainingSession->id)
        ];
    }
}
