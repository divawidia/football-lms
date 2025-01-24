<?php

namespace App\Notifications\CoachManagements\Admin;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CoachActivatedForAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected User $loggedUser;
    protected Coach $coach;

    public function __construct(User $loggedUser, Coach $coach)
    {
        $this->loggedUser= $loggedUser;
        $this->coach = $coach;
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
            'title' => "Coach Account activated",
            'data' => "Admin {$this->loggedUser->firstName} {$this->loggedUser->lastName} has updated coach {$this->coach->user->firstName} {$this->coach->user->lastName} account status to active. Please review the changes if necessary!",
            'redirectRoute' => route('coach-managements.show', $this->coach->hash),
        ];
    }
}
