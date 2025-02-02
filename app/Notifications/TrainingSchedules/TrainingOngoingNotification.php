<?php

namespace App\Notifications\TrainingSchedules;

use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingOngoingNotification extends Notification implements ShouldQueue
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
            ->subject("Training Session Starting Now!")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("Training session for {$this->team->teamName} is now on going." )
            ->line("Training Topic: {$this->training->eventName}")
            ->line("Team: {$this->team->teamName}")
            ->line("Location: {$this->training->location}")
            ->line("Date: ".convertToDate($this->training->date))
            ->line("Start Time: ".convertToTime($this->training->startTime))
            ->line("End Time: ".convertToTime($this->training->endTime))
            ->action('View training session detail', route('training-schedules.show', $this->training->hash))
            ->line("Please make your way to the training area and begin your session. We wish you a productive and successful training!")
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
            'title' => "Training Session Starting Now!",
            'data' => "The training session for {$this->team->teamName} scheduled at ".convertToDatetime($this->training->startDatetime)." is now starting at {$this->training->location}. Please proceed to the training area and begin your session.",
            'redirectRoute' => route('training-schedules.show', $this->training->hash)
        ];
    }
}
