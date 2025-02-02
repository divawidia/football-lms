<?php

namespace App\Notifications\TrainingSchedules;

use App\Models\Coach;
use App\Models\MatchModel;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingNoteDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected Training $training;
    protected Team $team;

    public function __construct(Training $training, Team $team)
    {
        $this->training = $training;
        $this->team = $team;
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
            'title' => "Training Note Deleted",
            'data' => 'A note for '.$this->team->teamName.' training session '.$this->training->topic.' has been deleted. Please check the note if needed!',
            'redirectRoute' => route('training-schedules.show', $this->training->hash)
        ];
    }
}
