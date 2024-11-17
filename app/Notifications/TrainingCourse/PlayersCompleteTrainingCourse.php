<?php

namespace App\Notifications\TrainingCourse;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlayersCompleteTrainingCourse extends Notification
{
    use Queueable;
    protected $trainingCourse;
    protected $completedDate;
    protected $role;
    protected $playerName;

    /**
     * Create a new notification instance.
     */
    public function __construct($trainingCourse, $role, $playerName = null, $completedDate)
    {
        $this->trainingCourse = $trainingCourse;
        $this->role = $role;
        $this->playerName = $playerName;
        $this->completedDate = $completedDate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->role == 'player') {
            $subject = "Congratulations on Completing Your Training Course";
            $openingMessage = "Congratulations on successfully completing the training course: {$this->trainingCourse->trainingTitle}";
            $closingMessage = "We’re proud of your hard work and dedication. Keep striving for excellence!";
        } else {
            $subject = "Player Training Course Completion";
            $openingMessage = "We’re excited to inform you that {$this->playerName} has successfully completed the training course: {$this->trainingCourse->trainingTitle}";
            $closingMessage = "Thank you for guiding and supporting their development.";
        }
        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("$openingMessage")
            ->action('View training course', route('training-videos.show', $this->trainingCourse->id))
            ->line($closingMessage);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->role == 'player') {
            $subject = "Congratulations! You have successfully completed the training course {$this->trainingCourse->trainingTitle} on {$this->completedDate}. Keep up the great work!";
        } else {
            $subject = "{$this->playerName} has successfully completed the training course {$this->trainingCourse->trainingTitle} on {$this->completedDate}. Great progress!";
        }
        return [
            'data' =>$subject,
            'redirectRoute' => route('training-videos.show', $this->trainingCourse->id),
        ];
    }
}
