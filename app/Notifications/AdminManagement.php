<?php

namespace App\Notifications;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminManagement extends Notification
{
    use Queueable;
    protected Admin $superAdmin;
    protected Admin $targetAdmin;
    protected string $status;

    public function __construct(Admin $superAdmin, string $status, Admin $targetAdmin = null)
    {
        $this->superAdmin = $superAdmin;
        $this->targetAdmin = $targetAdmin;
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

    private function message()
    {
        $route = route('admin-managements.show', $this->targetAdmin->hash);
        $data = "{$this->superAdmin->user->firstName} {$this->superAdmin->user->lastName} has {$this->status} an admin account : {$this->targetAdmin->user->firstName} {$this->targetAdmin->user->lastName}. Please review the changes if necessary!";
        $title = "Admin Account {$this->status}";
        if ($this->status == 'created') {
            $title = "New Admin Account {$this->status}";
            $data = "{$this->superAdmin->user->firstName} {$this->superAdmin->user->lastName} has {$this->status} a new admin account for {$this->targetAdmin->user->firstName} {$this->targetAdmin->user->lastName}. Please review the changes if necessary!";
        } elseif ($this->status == 'deleted') {
            $route = route('admin-managements.index');
        } elseif ($this->status == 'password') {
            $title = 'Admin Account Password Updated';
            $data = "{$this->superAdmin->user->firstName} {$this->superAdmin->user->lastName} has updated the password for admin account : {$this->targetAdmin->user->firstName} {$this->targetAdmin->user->lastName}. Please review the changes if necessary!";
        }
        return compact('title', 'data', 'route');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->message()['title'],
            'data' => $this->message()['data'],
            'redirectRoute' => $this->message()['route'],
        ];
    }
}
