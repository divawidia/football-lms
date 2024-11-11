<?php

namespace App\Notifications\AdminManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAccountUpdated extends Notification
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
            'data' =>"Your account have been {$this->status} by {$this->superAdminName}. Please review the changes if necessary.",
            'redirectRoute' => route('admin-managements.show', $this->newAdmin->id)
        ];
    }
}
