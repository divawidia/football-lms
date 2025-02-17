<?php

namespace App\Notifications\TrainingCourse\Player;

use App\Models\TrainingVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignPlayersToTrainingCourseForPlayers extends Notification implements ShouldQueue
{
    use Queueable;
    protected TrainingVideo $trainingCourse;

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
            ->subject("You Have Been Assigned to a New Training Course")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("You have been assigned to the training course: {$this->trainingCourse->trainingTitle}")
            ->line("Difficulty Level: {$this->trainingCourse->level}")
            ->action('View course detail', route('training-videos.show', $this->trainingCourse->hash))
            ->line("Please ensure you review the course materials and complete the course as soon as possible. You can find more details in your account dashboard.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'You’ve Been Assigned to a Training Course!',
            'data' =>"You have been assigned to the training course {$this->trainingCourse->trainingTitle}. Please check the course details and complete the course as soon as possible!",
            'redirectRoute' => route('training-videos.show', $this->trainingCourse->hash),
        ];
    }
}
