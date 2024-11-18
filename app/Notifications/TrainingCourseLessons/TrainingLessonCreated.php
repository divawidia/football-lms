<?php

namespace App\Notifications\TrainingCourseLessons;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingLessonCreated extends Notification
{
    use Queueable;
    protected $trainingCourse;
    protected $lesson;
    protected $createdByName;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingCourse, $lesson, $createdByName)
    {
        $this->trainingCourse = $trainingCourse;
        $this->lesson = $lesson;
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
            'data' =>"A new lesson titled '{$this->lesson->lessonTitle}' in training course {$this->trainingCourse->trainingTitle} has been successfully created by {$this->createdByName}. Please review the lesson details in the system!",
            'redirectRoute' => route('training-videos.lessons-show', ['trainingVideo' => $this->trainingCourse->id, 'lessons' => $this->lesson->id]),
        ];
    }
}
