<?php

namespace App\Notifications\TrainingSchedules;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingScheduleDeletedForPlayers extends Notification
{
    use Queueable;
    protected $trainingSchedule;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingSchedule)
    {
        $this->trainingSchedule = $trainingSchedule;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Training Session Schedule Deleted")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("The training session {$this->trainingSchedule->eventName} schedule at ".convertToDatetime($this->trainingSchedule->startDatetime)." has been deleted by ".$this->deletedBy)
            ->line("Training Topic: {$this->trainingSchedule->eventName}")
            ->line("Team: {$this->trainingSchedule->teams[0]->teamName}")
            ->line("Location: {$this->trainingSchedule->place}")
            ->line("Date: ".convertToDate($this->trainingSchedule->date))
            ->line("Start Time: ".convertToTime($this->trainingSchedule->startTime))
            ->line("End Time: ".convertToTime($this->trainingSchedule->endTime))
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
            'data' =>'The training session '.$this->trainingSchedule->eventName.' schedule for team '.$this->trainingSchedule->teams[0]->teamName.' at '.convertToDatetime($this->trainingSchedule->startDatetime).' has been deleted.',
            'redirectRoute' => route('training-schedules.index')
        ];
    }
}
