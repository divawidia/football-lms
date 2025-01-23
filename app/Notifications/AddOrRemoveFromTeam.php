<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AddOrRemoveFromTeam extends Notification implements ShouldQueue
{
    use Queueable;
    protected Team $team;
    protected string $status;

    public function __construct(Team $team, string $status)
    {
        $this->team = $team;
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

    public function message()
    {
        if ($this->status == 'added') {
            $title = "You have been {$this->status} to {$this->team->teamName}";
            $data = "Admin has been {$this->status} you to the {$this->team->teamName}";
        } else {
            $title = "You have been {$this->status} from {$this->team->teamName}";
            $data = "Admin has been {$this->status} you from {$this->team->teamName}";
        }
        return compact('data', 'title');
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
            'redirectRoute' => route('team-managements.show', $this->team->id)
        ];
    }
}
