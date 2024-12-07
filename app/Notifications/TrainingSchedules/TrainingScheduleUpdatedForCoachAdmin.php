<?php

namespace App\Notifications\TrainingSchedules;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingScheduleUpdatedForCoachAdmin extends Notification implements ShouldQueue
{
    use Queueable;
    protected $trainingSchedule;
    protected $team;
    protected $adminName;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingSchedule, $team, $adminName, $status)
    {
        $this->trainingSchedule = $trainingSchedule;
        $this->team = $team;
        $this->adminName = $adminName;
        $this->status = $status;
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
            ->subject("Training Session {$this->trainingSchedule->eventName} {$this->status}")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("The training session {$this->trainingSchedule->eventName} for your team {$this->team->teamName} on ".convertToDatetime($this->trainingSchedule->startDatetime)." {$this->status} by {$this->adminName}.")
            ->line("Training Topic: {$this->trainingSchedule->eventName}")
            ->line("Team: {$this->team->teamName}")
            ->line("Location: {$this->trainingSchedule->place}")
            ->line("Date: ".convertToDate($this->trainingSchedule->date))
            ->line("Start Time: ".convertToTime($this->trainingSchedule->startTime))
            ->line("End Time: ".convertToTime($this->trainingSchedule->endTime))
            ->action('View training session detail', route('training-schedules.show', $this->trainingSchedule->id))
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
            'data' =>'The training session '.$this->trainingSchedule->eventName.' for your team '.$this->team->teamName.' on '.convertToDatetime($this->trainingSchedule->startDatetime).' '.$this->status.' by '.$this->adminName.'. Please check the schedule for details!',
            'redirectRoute' => route('training-schedules.show', $this->trainingSchedule->id)
        ];
    }
}
