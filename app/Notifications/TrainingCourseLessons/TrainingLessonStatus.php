<?php

namespace App\Notifications\TrainingCourseLessons;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingLessonStatus extends Notification
{
    use Queueable;
    protected $trainingCourse;
    protected $lesson;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingCourse, $lesson, $status)
    {
        $this->trainingCourse = $trainingCourse;
        $this->lesson = $lesson;
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
            'data' =>"The lesson titled '{$this->lesson->lessonTitle}' in training course {$this->trainingCourse->trainingTitle} status has been set to {$this->status}. Please review the updated lesson details in the system!",
            'redirectRoute' => route('training-videos.lessons-show', ['trainingVideo' => $this->trainingCourse->id, 'lessons' => $this->lesson->id]),
        ];
    }
}
