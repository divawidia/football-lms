<?php

namespace App\Notifications\AdminManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAccountCreatedDeleted extends Notification
{
    use Queueable;
    protected $superAdminName;
    protected $newAdmin;
    protected $status;

    public function __construct($superAdminName, $newAdmin, $status)
    {
        $this->superAdminName = $superAdminName;
        $this->newAdmin = $newAdmin;
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
            'data' =>"{$this->superAdminName} has {$this->status} a new admin account for {$this->newAdmin->user->firstName} {$this->newAdmin->user->lastName}. Please review the changes if necessary.",
            'redirectRoute' => route('admin-managements.show', $this->newAdmin->id)
        ];
    }
}
