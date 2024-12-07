<?php

namespace App\Notifications\MatchSchedules;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchScheduleUpdatedForAdmin extends Notification implements ShouldQueue
{
    use Queueable;
    protected $matchSchedule;
    protected $updatedBy;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($matchSchedule, $updatedBy, $status)
    {
        $this->matchSchedule = $matchSchedule;
        $this->updatedBy = $updatedBy;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
//        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Match Session {$this->matchSchedule->teams[0]->teamName} Vs. {$this->matchSchedule->teams[1]->teamName} {$this->status}")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("The match session for your team {$this->matchSchedule->teams[0]->teamName} at ".convertToDatetime($this->matchSchedule->startDatetime)." {$this->status} by {$this->updatedBy}.")
            ->line("Match Teams: {$this->matchSchedule->teams[0]->teamName} Vs. {$this->matchSchedule->teams[1]->teamName}")
            ->line("Match Type: {$this->matchSchedule->matchType}")
            ->line("Location: {$this->matchSchedule->place}")
            ->line("Date: ".convertToDate($this->matchSchedule->date))
            ->line("Start Time: ".convertToTime($this->matchSchedule->startTime))
            ->line("End Time: ".convertToTime($this->matchSchedule->endTime))
            ->action('View match session detail', route('match-schedules.show', $this->matchSchedule->id))
            ->line("Please log in to view the details!")
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
            'data' =>'The match session for '.$this->matchSchedule->teams[0]->teamName.' Vs. '.$this->matchSchedule->teams[1]->teamName.' at '.convertToDatetime($this->matchSchedule->startDatetime).' '.$this->status.' by '.$this->updatedBy.'. Please check the schedule for details!',
            'redirectRoute' => route('match-schedules.show', $this->matchSchedule->id)
        ];
    }
}
