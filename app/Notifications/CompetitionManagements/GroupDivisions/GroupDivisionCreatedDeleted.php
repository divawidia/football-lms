<?php

namespace App\Notifications\CompetitionManagements\GroupDivisions;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupDivisionCreatedDeleted extends Notification
{
    use Queueable;
    protected $admin;
    protected $competition;
    protected $groupDivision;
    protected $status;

    public function __construct($admin, $groupDivision, $competition, $status)
    {
        $this->admin = $admin;
        $this->groupDivision = $groupDivision;
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
            'data' =>"Admin {$this->admin->firstName} {$this->admin->lastName} has {$this->status} a group division {$this->groupDivision->groupName} in competition {$this->competition->name}. Please review the changes if necessary.",
            'redirectRoute' => route('competition-managements.show', $this->competition->id)
        ];
    }
}
