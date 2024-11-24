<?php

namespace App\Notifications;

use App\Models\Coach;
use App\Models\EventSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayerPerformanceReview extends Notification
{
    use Queueable;
    protected Coach $coach;
    protected EventSchedule $event;
    protected string $action; // Either 'created', 'updated' or 'deleted'

    /**
     * Create a new notification instance.
     *
     * @param $coach
     * @param $trainingSession
     * @param string $action
     */
    public function __construct($coach, $action, $event = null)
    {
        $this->coach = $coach;
        $this->event = $event;
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
        if ($this->event->eventType == 'Training') {
            $message = 'Your performance review have been '.$this->action.' by coach '.$this->coach->user->firstName.' '.$this->coach->user->lastName.' in the '.$this->event->teams[0]->teamName.' training session '.$this->event->eventName.'.';
        } elseif ($this->event->eventType == 'Match') {
            $message = 'Your performance review have been '.$this->action.' by coach '.$this->coach->user->firstName.' '.$this->coach->user->lastName.' in the match '.$this->event->teams[0]->teamName.' Vs. '.$this->event->teams[1]->teamName.' session.';
        } else {
            $message = 'Your performance review have been '.$this->action.' by coach '.$this->coach->user->firstName.' '.$this->coach->user->lastName.'.';
        }
        return [
            'data' => $message,
            'redirectRoute' => route('player.performance-reviews')
        ];
    }
}
