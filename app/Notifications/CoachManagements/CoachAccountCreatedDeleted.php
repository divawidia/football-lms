<?php

namespace App\Notifications\CoachManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CoachAccountCreatedDeleted extends Notification
{
    use Queueable;
    protected $adminName;
    protected $coach;
    protected $status;

    public function __construct($adminName, $coach, $status)
    {
        $this->adminName = $adminName;
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
            'data' =>"{$this->adminName} has {$this->status} coach account for {$this->coach->user->firstName} {$this->coach->user->lastName}. Please review the changes if necessary.",
            'redirectRoute' => route('coach-managements.show', $this->coach->id)
        ];
    }
}
