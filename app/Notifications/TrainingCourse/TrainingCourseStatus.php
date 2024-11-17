<?php

namespace App\Notifications\TrainingCourse;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingCourseStatus extends Notification
{
    use Queueable;
    protected $trainingCourse;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingCourse, $status)
    {
        $this->trainingCourse = $trainingCourse;
        $this->status = $status;
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
            'data' =>"The training course titled '{$this->trainingCourse->trainingTitle}' status has been set to {$this->status}. Please review the updated course details in the system!",
            'redirectRoute' => route('training-videos.show', $this->trainingCourse->id),
        ];
    }
}
