<?php

namespace App\Notifications\TrainingCourse\AdminCoach;

use App\Models\TrainingVideo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrainingCourseDeleted extends Notification
{
    use Queueable;
    protected TrainingVideo $trainingCourse;
    protected User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(TrainingVideo $trainingCourse, User $user)
    {
        $this->trainingCourse = $trainingCourse;
        $this->user = $user;
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
            'title' => "Training course have been deleted",
            'data' =>"The training course {$this->trainingCourse->trainingTitle} has been deleted by admin ".getUserFullName($this->user).". Please review the change if necessary",
            'redirectRoute' => route('training-videos.index')
        ];
    }
}
