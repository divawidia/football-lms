<?php

namespace App\Notifications\TrainingCourseLessons;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingLessonUpdated extends Notification
{
    use Queueable;
    protected $trainingCourse;
    protected $lesson;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingCourse, $lesson, $createdByName)
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
            'data' =>"The lesson titled '{$this->lesson->lessonTitle}' in training course {$this->trainingCourse->trainingTitle} has been updated. Please review the updated lesson details in the system!",
            'redirectRoute' => route('training-videos.lessons-show', ['trainingVideo' => $this->trainingCourse->id, 'lessons' => $this->lesson->id]),
        ];
    }
}
