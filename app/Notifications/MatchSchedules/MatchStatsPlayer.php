<?php

namespace App\Notifications\MatchSchedules;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchStatsPlayer extends Notification
{
    use Queueable;
    protected $scheduleDetails;

    public function __construct($scheduleDetails)
    {
        $this->scheduleDetails = $scheduleDetails;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your Match Session {$this->scheduleDetails->teams[0]->teamName} Vs. {$this->scheduleDetails->teams[1]->teamName} Stats")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("Your stats of match session for your team {$this->scheduleDetails->teams[0]->teamName} at ".convertToDatetime($this->scheduleDetails->startDatetime)." have been updated.")
            ->line("Match Teams: {$this->scheduleDetails->teams[0]->teamName} Vs. {$this->scheduleDetails->teams[1]->teamName}")
            ->line("Match Type: {$this->scheduleDetails->matchType}")
            ->line("Location: {$this->scheduleDetails->place}")
            ->line("Date: ".convertToDate($this->scheduleDetails->date))
            ->line("Start Time: ".convertToTime($this->scheduleDetails->startTime))
            ->line("End Time: ".convertToTime($this->scheduleDetails->endTime))
            ->action('View match session detail', route('match-schedules.show', $this->scheduleDetails->id))
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
            'data' =>'Your match stats of the match session for your team '.$this->scheduleDetails->teams[0]->teamName.' at '.convertToDatetime($this->scheduleDetails->startDatetime).' have been updated. Check your stats now!',
            'redirectRoute' => route('match-schedules.show', $this->scheduleDetails->id)
        ];
    }
}
