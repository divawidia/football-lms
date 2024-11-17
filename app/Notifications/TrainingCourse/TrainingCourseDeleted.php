<?php

namespace App\Notifications\TrainingCourse;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingCourseDeleted extends Notification
{
    use Queueable;
    protected $trainingCourse;
    protected $deletedBy;

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
            'data' =>"The training course {$this->trainingCourse->trainingTitle} has been deleted from the system.",
            'redirectRoute' => route('training-videos.index')
        ];
    }
}
