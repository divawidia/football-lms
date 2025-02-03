<?php

namespace App\Notifications\MatchSchedules;

use App\Models\MatchModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchScheduleAttendanceNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected MatchModel $match;
    protected string $status;

    public function __construct(MatchModel $match, string $status)
    {
        $this->match = $match;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
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
            ->subject("Match Session Attendance Notification")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("This is a notification about your attendance for the Match session for your team {$this->match->teams[0]->teamName} at ".convertToDatetime($this->match->startDatetime).".")
            ->line("Team Match: {$this->matchTeams()}")
            ->line("Match Type: {$this->match->matchType}")
            ->line("Location: {$this->match->place}")
            ->line("Date: ".convertToDate($this->match->date))
            ->line("Start Time: ".convertToTime($this->match->startTime))
            ->line("End Time: ".convertToTime($this->match->endTime))
            ->line("Attendance Status: {$this->status}.")
            ->action('View match session detail', route('match-schedules.show', $this->match->id))
            ->line("Please ensure your attendance is marked accordingly.")
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
            'title' => "Match Session Attendance Status",
            'data' =>"'Your attendance for the {$this->matchTeams()} match session at ".convertToDatetime($this->match->date)." is marked as: {$this->status}",
            'redirectRoute' => route('match-schedules.show', $this->match->hash)
        ];
    }
}
