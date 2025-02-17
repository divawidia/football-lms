<?php

namespace App\Notifications\TrainingCourse\Player;

use App\Models\TrainingVideo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayersCompleteTrainingCourseForPlayer extends Notification
{
    use Queueable;
    protected TrainingVideo $trainingCourse;
    protected string $completedDate;

    /**
     * Create a new notification instance.
     */
    public function __construct(TrainingVideo $trainingCourse, string $completedDate)
    {
        $this->trainingCourse = $trainingCourse;
        $this->completedDate = $completedDate;
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
            ->subject("Congratulations on Completing Your Training Course")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("Congratulations on successfully completing the training course: {$this->trainingCourse->trainingTitle}")
            ->action('View training course', route('training-videos.show', $this->trainingCourse->hash))
            ->line("We’re proud of your hard work and dedication. Keep striving for excellence!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Training course completed",
            'data' => "Congratulations! You have successfully completed the {$this->trainingCourse->trainingTitle} training course. Keep up the great work!",
            'redirectRoute' => route('training-videos.show', $this->trainingCourse->hash),
        ];
    }
}
