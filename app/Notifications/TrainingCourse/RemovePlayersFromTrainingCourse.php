<?php

namespace App\Notifications\TrainingCourse;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RemovePlayersFromTrainingCourse extends Notification
{
    use Queueable;
    protected $trainingCourse;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingCourse)
    {
        $this->trainingCourse = $trainingCourse;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Removed from Training Course")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("We want to inform you that you have been removed from the training course: {$this->trainingCourse->trainingTitle}")
            ->action('View your assigned courses', route('training-videos.index'))
            ->line("If you believe this was a mistake or have any questions, please contact your coach or administrator.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' =>"You have been removed from the training course {$this->trainingCourse->trainingTitle}. If you have any questions, please contact your coach or administrator.",
            'redirectRoute' => route('training-videos.index'),
        ];
    }
}
