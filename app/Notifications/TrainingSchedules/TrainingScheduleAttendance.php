<?php

namespace App\Notifications\TrainingSchedules;

use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingScheduleAttendance extends Notification implements ShouldQueue
{
    use Queueable;
    protected Training $training;
    protected string $status;

    public function __construct(Training $training, string $status)
    {
        $this->training = $training;
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Training Session Attendance Notification")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("This is a notification about your attendance for the training session for your team {$this->training->team->teamName} at ".convertToDatetime($this->training->startDatetime).".")
            ->line("Training Topic: {$this->training->topic}")
            ->line("Team: {$this->training->team->teamName}")
            ->line("Location: {$this->training->location}")
            ->line("Date: ".convertToDate($this->training->date))
            ->line("Start Time: ".convertToTime($this->training->startTime))
            ->line("End Time: ".convertToTime($this->training->endTime))
            ->line("Attendance Status: {$this->status}.")
            ->action('View training session detail', route('training-schedules.show', $this->training->hash))
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
            'title' => "Training Session Attendance Status",
            'data' =>'Your attendance for the '.$this->training->topic.' training session for your team '.$this->training->team->teamName.' at '.convertToDatetime($this->training->startDatetime).' is marked as: '.$this->status,
            'redirectRoute' => route('training-schedules.show', $this->training->hash)
        ];
    }
}
