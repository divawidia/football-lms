<?php

namespace App\Notifications\PlayerManagements;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayerChangeForPlayer extends Notification
{
    use Queueable;
    protected string $status;

    public function __construct(string $status)
    {
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
            'title' => $this->message()['title'],
            'data' => $this->message()['data'],
            'redirectRoute' => route('edit-account.edit')
        ];
    }

    public function message()
    {
        $title = "Account has been {$this->status}";
        $data = "Your account has been {$this->status} by Admin. Please review the changes if necessary!";

        if ($this->status == 'password'){
            $title = 'Account password has been updated';
            $data = "Your account password has been updated by Admin. Please review the changes if necessary!";
        }
        return compact('title', 'data');
    }
}
