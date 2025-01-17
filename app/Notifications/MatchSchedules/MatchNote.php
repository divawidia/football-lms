<?php

namespace App\Notifications\MatchSchedules;

use App\Models\Coach;
use App\Models\Match;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchNote extends Notification implements ShouldQueue
{
    use Queueable;
    protected Match $matchSession;
    protected string $action; // Either 'created', 'updated' or 'deleted'

    public function __construct($user, $matchSession, $action)
    {
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
            'data' => 'A note for '.$this->matchSession->teams[0]->teamName.' Vs. '.$this->matchSession->teams[1]->teamName.' match session has been '.$this->action.'. Please check the note if needed!',
            'redirectRoute' => route('match-schedules.show', $this->matchSession->id)
        ];
    }
}
