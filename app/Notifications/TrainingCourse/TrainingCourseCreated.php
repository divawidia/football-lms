<?php

namespace App\Notifications\TrainingCourse;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingCourseCreated extends Notification
{
    use Queueable;
    protected $trainingCourse;
    protected $createdByName;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingCourse, $createdByName)
    {
        $this->trainingCourse = $trainingCourse;
        $this->createdByName = $createdByName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' =>"A new training course titled '{$this->trainingCourse->trainingTitle}' has been successfully created by {$this->createdByName}. Please review the course details in the system!",
            'redirectRoute' => route('training-videos.show', $this->trainingCourse->id),
        ];
    }
}
