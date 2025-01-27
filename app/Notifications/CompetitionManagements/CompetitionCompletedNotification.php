<?php

namespace App\Notifications\CompetitionManagements;

use App\Models\Competition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompetitionCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected Competition $competition;

    public function __construct($competition)
    {
        $this->competition = $competition;
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
            'title' => "Competition Ended",
            'data' =>"The {$this->competition->name} competition has ended.",
            'redirectRoute' => route('competition-managements.show', $this->competition->hash)
        ];
    }
}
