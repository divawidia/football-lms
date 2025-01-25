<?php

namespace App\Notifications\AdminManagements;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminDeletedNotification extends Notification
{
    use Queueable;
    protected Admin $superAdmin;
    protected Admin $targetAdmin;

    public function __construct(Admin $superAdmin, Admin $targetAdmin = null)
    {
        $this->superAdmin = $superAdmin;
        $this->targetAdmin = $targetAdmin;
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
            'title' => "Admin Account deleted",
            'data' => "Admin {$this->superAdmin->user->firstName} {$this->superAdmin->user->lastName} has deleted admin {$this->targetAdmin->user->firstName} {$this->targetAdmin->user->lastName}. Please review the changes if necessary!",
            'redirectRoute' => route('admin-managements.index'),
        ];
    }
}
