<?php

namespace App\Notifications\TrainingSchedules;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingScheduleCreatedForCoachAdmin extends Notification implements ShouldQueue
{
    use Queueable;
    protected $trainingSchedule;
    protected $team;
    protected $adminName;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingSchedule, $team, $adminName)
    {
        $this->trainingSchedule = $trainingSchedule;
        $this->team = $team;
        $this->adminName = $adminName;
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
            ->subject("New Training Session Scheduled")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("A new training session {$this->trainingSchedule->eventName} has been scheduled on ".convertToDatetime($this->trainingSchedule->startDatetime)." by admin ".$this->adminName.". Please log in to view the details.")
            ->line("Training Topic: {$this->trainingSchedule->eventName}")
            ->line("Team: {$this->team->teamName}")
            ->line("Location: {$this->trainingSchedule->place}")
            ->line("Date: ".convertToDate($this->trainingSchedule->date))
            ->line("Start Time: ".convertToTime($this->trainingSchedule->startTime))
            ->line("End Time: ".convertToTime($this->trainingSchedule->endTime))
            ->action('View training session detail', route('training-schedules.show', $this->trainingSchedule->id))
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
            'data' =>'A new training session '.$this->trainingSchedule->eventName.' for team '.$this->team->teamName.' has been scheduled on '.convertToDatetime($this->trainingSchedule->startDatetime).' by '.$this->adminName.'. Please check the details and prepare accordingly!',
            'redirectRoute' => route('training-schedules.show', $this->trainingSchedule->id)
        ];
    }
}
