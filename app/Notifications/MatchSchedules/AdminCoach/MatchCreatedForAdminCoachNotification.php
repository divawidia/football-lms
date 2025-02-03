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

class MatchCreatedForAdminCoachNotification extends Notification implements ShouldQueue
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
            ->subject("New Match Session Scheduled")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("A new match schedule for {$this->matchTeams()} has been added by admin {$this->loggedUser->firstName} {$this->loggedUser->lastName}." )
            ->line("Team Match: {$this->matchTeams()}")
            ->line("Venue: {$this->match->place}")
            ->line("Date: ".convertToDate($this->match->date))
            ->line("Start Time: ".convertToTime($this->match->startTime))
            ->line("End Time: ".convertToTime($this->match->endTime))
            ->action('View match session detail', route('match-schedules.show', $this->match->hash))
            ->line("Please check the match schedule for more information!")
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
            'title' => "New match Schedule Created",
            'data' => "Admin {$this->loggedUser->firstName} {$this->loggedUser->lastName} has created a New match schedule for {$this->matchTeams()} scheduled at ".convertToDatetime($this->match->startDatetime).". Please review the schedule and prepare accordingly!",
            'redirectRoute' => route('match-schedules.show', $this->match->hash)
        ];
    }
}
