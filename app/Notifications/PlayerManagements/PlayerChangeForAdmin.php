<?php

namespace App\Notifications\PlayerManagements;

use App\Models\Player;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlayerChangeForAdmin extends Notification
{
    use Queueable;
    protected User $admin;
    protected Player $player;
    protected string $status;

    public function __construct(User $admin, Player $player, string $status)
    {
        $this->admin = $admin;
        $this->player = $player;
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
            'redirectRoute' => route('player-managements.show', $this->player->hash)
        ];
    }

    public function message()
    {
        $title = "Player account has been {$this->status}";
        $data = "Admin {$this->admin->firstName} {$this->admin->lastName} has been {$this->status} player {$this->player->user->firstName} {$this->player->user->lastName} account. Please review the changes if necessary!";

        if ($this->status == 'password'){
            $title = "Player account password has been updated";
            $data = "Admin {$this->admin->firstName} {$this->admin->lastName} has been updated password for player {$this->player->user->firstName} {$this->player->user->lastName} account. Please review the changes if necessary!";
        } elseif ($this->status == 'create') {
            $title = "New player account has been created";
            $data = "Admin {$this->admin->firstName} {$this->admin->lastName} has been created a new player {$this->player->user->firstName} {$this->player->user->lastName} account. Please review the changes if necessary!";
        }
        return compact('title', 'data');
    }
}
