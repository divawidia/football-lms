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
    protected $role;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingCourse, $lesson, $createdByName, $role)
    {
        $this->trainingCourse = $trainingCourse;
        $this->lesson = $lesson;
        $this->createdByName = $createdByName;
        $this->role = $role;
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
        if ($this->role == 'player') {
            $message = "A new lesson titled '{$this->lesson->lessonTitle}' in training course {$this->trainingCourse->trainingTitle} has been added and assigned to you. Please check the lesson details in the system and complete as soos as possible!";
        } else {
            $message = "A new lesson titled '{$this->lesson->lessonTitle}' in training course {$this->trainingCourse->trainingTitle} has been successfully created by {$this->createdByName}. Please review the lesson details in the system!";
        }
        return [
            'data' => $message,
            'redirectRoute' => route('training-videos.lessons-show', ['trainingVideo' => $this->trainingCourse->id, 'lessons' => $this->lesson->id]),
        ];
    }
}
