<?php

namespace App\Notifications\TrainingCourseLessons\Admin;

use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrainingLessonDeletedForAdmin extends Notification
{
    use Queueable;
    protected TrainingVideo $trainingCourse;
    protected TrainingVideoLesson $lesson;
    protected User $createdBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(TrainingVideo $trainingCourse, TrainingVideoLesson $lesson, User $createdBy)
    {
        $this->trainingCourse = $trainingCourse;
        $this->lesson = $lesson;
        $this->createdBy = $createdBy;
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
            'title' => 'Training Video Lesson Deleted',
            'data' =>"The training video lesson : '{$this->lesson->lessonTitle}' in {$this->trainingCourse->trainingTitle} training course has been deleted by admin ".getUserFullName($this->createdBy).". Please review the deleted training video lesson!",
            'redirectRoute' => route('training-videos.show', ['trainingVideo' => $this->trainingCourse->hash]),
        ];
    }
}
