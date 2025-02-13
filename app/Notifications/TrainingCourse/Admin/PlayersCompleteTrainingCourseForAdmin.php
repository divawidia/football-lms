<?php

namespace App\Notifications\TrainingCourse\Admin;

use App\Models\TrainingVideo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayersCompleteTrainingCourseForAdmin extends Notification
{
    use Queueable;
    protected TrainingVideo $trainingCourse;
    protected string $completedDate;
    protected User $playerUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(TrainingVideo $trainingCourse, string $completedDate, User $playerUser)
    {
        $this->trainingCourse = $trainingCourse;
        $this->playerUser = $playerUser;
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
            ->subject("Player Have been completed Training Course")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("We’re excited to inform you that ".getUserFullName($this->playerUser)." has successfully completed the {$this->trainingCourse->trainingTitle} training course at {$this->completedDate}.")
            ->action('View training course', route('training-videos.show', $this->trainingCourse->hash))
            ->line("Thank you for guiding and supporting their development.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Player Have been completed Training Course",
            'data' => "Player ".getUserFullName($this->playerUser)." has been successfully completed the {$this->trainingCourse->trainingTitle} training course at {$this->completedDate}. Please review the player progress if necessary!!",
            'redirectRoute' => route('training-videos.show', $this->trainingCourse->hash),
        ];
    }
}
