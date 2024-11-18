<?php

namespace App\Notifications\TrainingCourseLessons;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingLessonDeleted extends Notification
{
    use Queueable;
    protected $trainingCourse;
    protected $lesson;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingCourse, $lesson)
    {
        $this->trainingCourse = $trainingCourse;
        $this->lesson = $lesson;
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
            'data' =>"The lesson titled '{$this->lesson->lessonTitle}' in training course {$this->trainingCourse->trainingTitle} has been deleted from the system. Please review the deleted lesson in the system!",
            'redirectRoute' => route('training-videos.show', ['trainingVideo' => $this->trainingCourse->id]),
        ];
    }
}
