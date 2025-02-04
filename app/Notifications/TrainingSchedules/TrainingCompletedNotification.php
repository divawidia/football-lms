<?php

namespace App\Notifications\TrainingSchedules;

use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected Training $training;
    protected Team $team;

    /**
     * Create a new notification instance.
     */
    public function __construct(Training $training, Team $team)
    {
        $this->training = $training;
        $this->team = $team;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'mail',
            'database'
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Training Session Completed!")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("Training session for {$this->team->teamName} has been successfully completed." )
            ->line("Training Topic: {$this->training->topic}")
            ->line("Team: {$this->team->teamName}")
            ->line("Location: {$this->training->location}")
            ->line("Date: ".convertToDate($this->training->date))
            ->line("Start Time: ".convertToTime($this->training->startTime))
            ->line("End Time: ".convertToTime($this->training->endTime))
            ->action('View training session detail', route('training-schedules.show', $this->training->hash))
            ->line("Great job participating! Keep up the hard work, and we look forward to seeing you in the next session.")
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
            'title' => "Training Session Completed",
            'data' => "The training session for {$this->team->teamName} scheduled at ".convertToDatetime($this->training->startDatetime)." has been successfully completed. Great job, keep practicing and stay prepared for the next session.",
            'redirectRoute' => route('training-schedules.show', $this->training->hash)
        ];
    }
}
