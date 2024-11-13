<?php

namespace App\Notifications\CompetitionManagements\GroupDivisions;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupDivisionUpdated extends Notification
{
    use Queueable;
    protected $admin;
    protected $competition;
    protected $groupDivision;
    protected $status;

    public function __construct($admin, $competition, $groupDivision, $status)
    {
        $this->admin = $admin;
        $this->competition = $competition;
        $this->groupDivision = $groupDivision;
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
            'data' =>"Group division {$this->groupDivision->groupName} in competition {$this->competition->name} have been {$this->status} by Admin {$this->admin->firstName} {$this->admin->lastName}. Please review the changes if necessary.",
            'redirectRoute' => route('competition-managements.show', $this->competition->id)
        ];
    }
}
