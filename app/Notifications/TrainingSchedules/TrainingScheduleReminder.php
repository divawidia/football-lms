<?php

namespace App\Notifications\TrainingSchedules;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingScheduleReminder extends Notification implements ShouldQueue
{
    use Queueable;
    protected $trainingSchedule;
    protected $team;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingSchedule, $team)
    {
        $this->trainingSchedule = $trainingSchedule;
        $this->team = $team;
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
            ->subject("Training Session Reminder")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("Reminder: You have training session {$this->trainingSchedule->eventName} for your team {$this->team->teamName} scheduled at ".convertToDatetime($this->trainingSchedule->startDatetime).".")
            ->line("Training Topic: {$this->trainingSchedule->eventName}")
            ->line("Team: {$this->team->teamName}")
            ->line("Location: {$this->trainingSchedule->place}")
            ->line("Date: ".convertToDate($this->trainingSchedule->date))
            ->line("Start Time: ".convertToTime($this->trainingSchedule->startTime))
            ->line("End Time: ".convertToTime($this->trainingSchedule->endTime))
            ->action('View training session detail', route('training-schedules.show', $this->trainingSchedule->id))
            ->line("Please be on time!")
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
            'data' =>'Reminder! : The training session '.$this->trainingSchedule->eventName.' for your team '.$this->team->teamName.' at '.convertToDatetime($this->trainingSchedule->startDatetime).'. Please arrive on time!',
            'redirectRoute' => route('training-schedules.show', $this->trainingSchedule->id)
        ];
    }
}
