<?php

namespace App\Notifications\CompetitionManagements;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompetitionScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected User $loggedUser;
    protected Competition $competition;

    public function __construct($loggedUser, $competition)
    {
        $this->loggedUser = $loggedUser;
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
            'title' => "Competition Scheduled",
            'data' =>"Admin {$this->loggedUser->firstName} {$this->loggedUser->lastName} has set the upcoming {$this->competition->name} competition to scheduled. Please review the change if necessary!.",
            'redirectRoute' => route('competition-managements.show', $this->competition->hash)
        ];
    }
}
