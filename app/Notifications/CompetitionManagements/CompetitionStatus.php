<?php

namespace App\Notifications\CompetitionManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompetitionStatus extends Notification implements ShouldQueue
{
    use Queueable;
    protected $competition;
    protected $status;

    public function __construct($competition, $status)
    {
        $this->competition = $competition;
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
            'title' => "Competition {$this->status}",
            'data' =>"The competition {$this->competition->name} {$this->status}.",
            'redirectRoute' => route('competition-managements.show', $this->competition->hash)
        ];
    }
}
