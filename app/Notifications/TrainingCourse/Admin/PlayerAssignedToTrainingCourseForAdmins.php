<?php

namespace App\Notifications\TrainingCourse\Admin;

use App\Models\TrainingVideo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PlayerAssignedToTrainingCourseForAdmins extends Notification implements ShouldQueue
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
            'title' => 'New Players have been assigned to Training Course',
            'data' =>"New players have been assigned to the '{$this->trainingCourse->trainingTitle}' training course by admin ".getUserFullName($this->user).". Please review the training course!",
            'redirectRoute' => route('training-videos.show', $this->trainingCourse->hash),
        ];
    }
}
