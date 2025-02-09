<?php

namespace App\Notifications\TrainingCourseLessons\Player;

use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TrainingLessonCreatedForPlayer extends Notification implements ShouldQueue
{
    use Queueable;
    protected TrainingVideo $trainingCourse;
    protected TrainingVideoLesson $lesson;

    /**
     * Create a new notification instance.
     */
    public function __construct(TrainingVideo $trainingCourse, TrainingVideoLesson $lesson)
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
            'title' => "New Training video Lesson Assigned to you",
            'data' => "A new training video lesson : '{$this->lesson->lessonTitle}' in {$this->trainingCourse->trainingTitle} training course has been added and assigned to you. Please check the training video lesson and complete as soon as possible!",
            'redirectRoute' => route('training-videos.lessons-show', ['trainingVideo' => $this->trainingCourse->hash, 'lesson' => $this->lesson->hash]),
        ];
    }
}
