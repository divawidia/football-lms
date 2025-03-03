<?php

namespace App\Notifications\MatchSchedules;

use App\Models\MatchModel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected MatchModel $match;

    /**
     * Create a new notification instance.
     */
    public function __construct(MatchModel $match)
    {
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
            ->subject("Match Session Completed")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("Match session for {$this->matchTeams()} has been successfully completed.")
            ->line("Team Match: {$this->matchTeams()}")
            ->line("Venue: {$this->match->place}")
            ->line("Date: ".convertToDate($this->match->date))
            ->line("Start Time: ".convertToTime($this->match->startTime))
            ->line("End Time: ".convertToTime($this->match->endTime))
            ->action('View match session detail', route('match-schedules.show', $this->match->hash))
            ->line("Great job participating! Keep up the hard work, and we look forward to seeing you in the next session!")
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
            'title' => "match Session Completed",
            'data' => "The match session for {$this->matchTeams()} scheduled at ".convertToDatetime($this->match->startDatetime)." has been successfully completed. Great job, keep practicing and stay prepared for the next session.",
            'redirectRoute' => route('match-schedules.show', $this->match->hash)
        ];
    }
}
