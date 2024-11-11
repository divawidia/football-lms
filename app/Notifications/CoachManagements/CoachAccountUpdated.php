<?php

namespace App\Notifications\CoachManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CoachAccountUpdated extends Notification
{
    use Queueable;
    protected $coach;
    protected $status;

    public function __construct($coach, $status)
    {
        $this->coach = $coach;
        $this->status = $status;
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
            'data' =>"Your account have been {$this->status} by Admin. Please review the changes if necessary.",
            'redirectRoute' => route('coach-managements.show', $this->coach->id)
        ];
    }
}
