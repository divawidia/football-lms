<?php

namespace App\Notifications\MatchSchedules;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchScheduleDeletedForAdmin extends Notification implements ShouldQueue
{
    use Queueable;
    protected $matchSchedule;
    protected $deletedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($matchSchedule, $deletedBy)
    {
        $this->matchSchedule = $matchSchedule;
        $this->deletedBy = $deletedBy;
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
            ->subject("Match Session Deleted")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("The match session for your team {$this->matchSchedule->teams[0]->teamName} scheduled at ".convertToDatetime($this->matchSchedule->startDatetime)." by admin ".$this->deletedBy.". Please log in to view the details.")
            ->line("Match Teams: {$this->matchSchedule->teams[0]->teamName} Vs. {$this->matchSchedule->teams[1]->teamName}")
            ->line("Match Type: {$this->matchSchedule->matchType}")
            ->line("Location: {$this->matchSchedule->place}")
            ->line("Date: ".convertToDate($this->matchSchedule->date))
            ->line("Start Time: ".convertToTime($this->matchSchedule->startTime))
            ->line("End Time: ".convertToTime($this->matchSchedule->endTime))
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
            'data' =>'The match session for your team '.$this->matchSchedule->teams[0]->teamName.' scheduled at '.convertToDatetime($this->matchSchedule->startDatetime).' has been deleted by '.$this->deletedBy.'. Please check the details!',
            'redirectRoute' => route('match-schedules.index')
        ];
    }
}
