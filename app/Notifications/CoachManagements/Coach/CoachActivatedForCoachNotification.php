<?php

namespace App\Notifications\CoachManagements\Coach;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CoachActivatedForCoachNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public function __construct()
    {
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
            'title' => "Your Account has been activated",
            'data' => "Your account has been activated by admin. Please review the changes if necessary!",
            'redirectRoute' => route('edit-account.edit'),
        ];
    }
}
