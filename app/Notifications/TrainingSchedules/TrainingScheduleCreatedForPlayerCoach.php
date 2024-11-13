<?php

namespace App\Notifications\TrainingSchedules;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingScheduleCreatedForPlayerCoach extends Notification
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
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Training Session Scheduled")
            ->greeting("Hello!")
            ->line("A new training session {$this->trainingSchedule->eventName} for your team {$this->team->teamName} has been scheduled on ".convertToDatetime($this->trainingSchedule->startDatetime).". Please log in to view the details.")
            ->action('View training session detail at', route('training-schedules.show', $this->trainingSchedule->id))
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
            'data' =>'A new training session '.$this->trainingSchedule->eventName.' for your team '.$this->team->teamName.' has been scheduled on '.convertToDatetime($this->trainingSchedule->startDatetime).'. Please check the details and prepare accordingly!',
            'redirectRoute' => route('training-schedules.show', $this->trainingSchedule->id)
        ];
    }
}
