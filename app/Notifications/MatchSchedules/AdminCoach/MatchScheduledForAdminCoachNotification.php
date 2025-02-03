<?php

namespace App\Notifications\MatchSchedules\AdminCoach;

use App\Models\MatchModel;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchScheduledForAdminCoachNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected User $loggedUser;
    protected MatchModel $match;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $loggedUser, MatchModel $match)
    {
        $this->loggedUser = $loggedUser;
        $this->match = $match;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'mail',
            'database'
        ];
    }

    private function matchTeams()
    {
        if ($this->match->matchType == 'Internal Match') {
            return $this->match->homeTeam->teamName. " Vs. ". $this->match->awayTeam->teamName;
        } else {
            return $this->match->homeTeam->teamName. " Vs. ". $this->match->externalTeam->teamName;
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Match Session Scheduled")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("A match session for {$this->matchTeams()} has been updated by admin {$this->loggedUser->firstName} {$this->loggedUser->lastName}.")
            ->line("Team Match: {$this->matchTeams()}")
            ->line("Venue: {$this->match->place}")
            ->line("Date: ".convertToDate($this->match->date))
            ->line("Start Time: ".convertToTime($this->match->startTime))
            ->line("End Time: ".convertToTime($this->match->endTime))
            ->action('View match session detail', route('match-schedules.show', $this->match->hash))
            ->line("Please check the match schedule for more information and prepare accordingly!")
            ->line("If you have any questions or require further information, please don't hesitate to reach out.!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "match Schedule Updated",
            'data' => "Admin {$this->loggedUser->firstName} {$this->loggedUser->lastName} has set the match schedule for {$this->matchTeams()} to scheduled. Please review the schedule and prepare accordingly!",
            'redirectRoute' => route('match-schedules.show', $this->match->hash)
        ];
    }
}
