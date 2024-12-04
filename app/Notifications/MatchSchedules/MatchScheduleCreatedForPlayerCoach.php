<?php

namespace App\Notifications\MatchSchedules;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchScheduleCreatedForPlayerCoach extends Notification
{
    use Queueable;
    protected $matchSchedule;

    /**
     * Create a new notification instance.
     */
    public function __construct($matchSchedule)
    {
        $this->matchSchedule = $matchSchedule;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
//        return ['mail', 'database'];
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Match Session Scheduled")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("A new match session for your team {$this->matchSchedule->teams[0]->teamName} has been scheduled at ".convertToDatetime($this->matchSchedule->startDatetime).". Please log in to view the details.")
            ->line("Match Teams: {$this->matchSchedule->teams[0]->teamName} Vs. {$this->matchSchedule->teams[1]->teamName}")
            ->line("Match Type: {$this->matchSchedule->matchType}")
            ->line("Location: {$this->matchSchedule->place}")
            ->line("Date: ".convertToDate($this->matchSchedule->date))
            ->line("Start Time: ".convertToTime($this->matchSchedule->startTime))
            ->line("End Time: ".convertToTime($this->matchSchedule->endTime))
            ->action('View match session detail', route('match-schedules.show', $this->matchSchedule->id))
            ->line("Please prepare accordingly and arrive on time!")
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
            'data' =>'A new match session for your team '.$this->matchSchedule->teams[0]->teamName.' has been scheduled at '.convertToDatetime($this->matchSchedule->startDatetime).'. Please check the details and please be prepared for your upcoming match!',
            'redirectRoute' => route('match-schedules.show', $this->matchSchedule->id)
        ];
    }
}
