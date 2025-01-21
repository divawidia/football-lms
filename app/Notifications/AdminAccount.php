<?php

namespace App\Notifications\AdminManagements;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAccountCreatedDeleted extends Notification
{
    use Queueable;
    protected Admin $superAdmin;
    protected Admin $newAdmin;
    protected string $status;

    public function __construct(Admin $superAdmin, string $status, Admin $newAdmin = null)
    {
        $this->superAdmin = $superAdmin;
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

    private function message()
    {
        if ($this->status == 'create') {
            $title = 'New Admin Account Created';
            $data = "{$this->superAdmin->user->firstName} {$this->superAdmin->user->lastName} has created a new admin account for {$this->newAdmin->user->firstName} {$this->newAdmin->user->lastName}. Please review the changes if necessary.";
        } elseif ($this->status == 'delete') {
            $title = 'Admin Account Deleted';
            $data = "{$this->superAdmin->user->firstName} {$this->superAdmin->user->lastName} has deleted an admin account : {$this->newAdmin->user->firstName} {$this->newAdmin->user->lastName}. Please review the changes if necessary.";
        } elseif ($this->status == 'update') {
            $title = 'Admin Account Updated';
            $data = "{$this->superAdmin->user->firstName} {$this->superAdmin->user->lastName} has updated an admin account : {$this->newAdmin->user->firstName} {$this->newAdmin->user->lastName}. Please review the changes if necessary.";
        } elseif ($this->status == 'active') {
            $title = 'Admin Account Activated';
            $data = "{$this->superAdmin->user->firstName} {$this->superAdmin->user->lastName} has activated an admin account : {$this->newAdmin->user->firstName} {$this->newAdmin->user->lastName}. Please review the changes if necessary.";
        } elseif ($this->status == 'deactive') {
            $title = 'Admin Account Deactivated';
            $data = "{$this->superAdmin->user->firstName} {$this->superAdmin->user->lastName} has deactivated an admin account : {$this->newAdmin->user->firstName} {$this->newAdmin->user->lastName}. Please review the changes if necessary.";
        } elseif ($this->status == 'password') {
            $title = 'Admin Account Password Updated';
            $data = "{$this->superAdmin->user->firstName} {$this->superAdmin->user->lastName} has updated the password for admin account : {$this->newAdmin->user->firstName} {$this->newAdmin->user->lastName}. Please review the changes if necessary.";
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => '',
            'data' =>"{$this->superAdminName} has {$this->status} a new admin account for {$this->newAdmin->user->firstName} {$this->newAdmin->user->lastName}. Please review the changes if necessary.",
            'redirectRoute' => route('admin-managements.show', $this->newAdmin->id)
        ];
    }
}
