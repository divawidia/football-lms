<?php

namespace App\Notifications\CompetitionManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompetitionCreatedDeleted extends Notification implements ShouldQueue
{
    use Queueable;
    protected $admin;
    protected $competition;
    protected $status;

    public function __construct($admin, $competition, $status)
    {
        $this->admin = $admin;
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
            'data' =>"{$this->admin->firstName} {$this->admin->lastName} has {$this->status} a competition {$this->competition->name}. Please review the changes if necessary.",
            'redirectRoute' => route('competition-managements.show', $this->competition->hash)
        ];
    }
}
