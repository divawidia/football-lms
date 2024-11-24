<?php

namespace App\Notifications\MatchSchedules;

use App\Models\Coach;
use App\Models\EventSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchNote extends Notification
{
    use Queueable;
    protected Coach $coach;
    protected EventSchedule $matchSession;
    protected string $action; // Either 'created', 'updated' or 'deleted'

    /**
     * Create a new notification instance.
     *
     * @param $coach
     * @param $matchSession
     * @param string $action
     */
    public function __construct($coach, $matchSession, $action)
    {
        $this->coach = $coach;
        $this->matchSession = $matchSession;
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
            'data' =>'Coach '.$this->coach->user->firstName.' '.$this->coach->user->lastName.' has '.$this->action.' a note for '.$this->matchSession->teams[0]->teamName.' Vs. '.$this->matchSession->teams[1]->teamName.' match session. Please check the note if needed!',
            'redirectRoute' => route('match-schedules.show', $this->matchSession->id)
        ];
    }
}
