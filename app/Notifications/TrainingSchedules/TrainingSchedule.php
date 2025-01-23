<?php

namespace App\Notifications\TrainingSchedules;

use App\Models\Team;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingSchedule extends Notification implements ShouldQueue
{
    use Queueable;
    protected Training $training;
    protected Team $team;
    protected string $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Training $training, Team $team, $status)
    {
        $this->training = $training;
        $this->team = $team;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'mail',
            'database'
        ];
    }

    private function messageText()
    {
        $subjectText = '';
        $openingLine = "Training session {$this->team->teamName} scheduled at ".convertToDatetime($this->training->startDatetime);
        $closingLine = "Please check the training schedule for more information!";

        if ($this->status == 'create') {
            $subjectText = "New Training Session Scheduled";
            $openingLine = "A new ".$openingLine;
        } elseif ($this->status == 'delete') {
            $subjectText = "Training Session Deleted";
            $openingLine = $openingLine." has been deleted.";
        } elseif ($this->status == 'update') {
            $subjectText = "Training Session Updated";
            $openingLine = $openingLine." has been updated.";
        } elseif ($this->status == 'reminder') {
            $subjectText = "Training Session Reminder";
            $openingLine = $openingLine." start tomorrow.";
            $closingLine = "Please arrive on time and be prepared for your tomorrow training!";
        } elseif ($this->status == 'ongoing') {
            $subjectText = "Training Session is Ongoing";
            $openingLine = $openingLine." is now ongoing.";
        } elseif ($this->status == 'completed') {
            $subjectText = "Training Session Have Been Completed";
            $openingLine = $openingLine." have been completed.";
        } elseif ($this->status == 'cancelled') {
            $subjectText = "Training Session Have Been Cancelled";
            $openingLine = $openingLine." have been cancelled.";
        } elseif ($this->status == 'scheduled') {
            $subjectText = "Training Session Have Been Set to Scheduled";
            $openingLine = $openingLine." have been set to scheduled.";
        }
        $systemNotifText = $openingLine." ".$closingLine;

        return compact('subjectText', 'openingLine', 'closingLine', 'systemNotifText');
    }

    private function rediredtRoute()
    {
        if ($this->status == 'delete') {
            return route('training-schedules.index');
        } else {
            return route('training-schedules.show', $this->training->hash);
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->messageText()['subjectText'])
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line($this->messageText()['openingLine'])
            ->line("Training Topic: {$this->training->eventName}")
            ->line("Team: {$this->team->teamName}")
            ->line("Location: {$this->training->place}")
            ->line("Date: ".convertToDate($this->training->date))
            ->line("Start Time: ".convertToTime($this->training->startTime))
            ->line("End Time: ".convertToTime($this->training->endTime))
            ->action('View training session detail', route('training-schedules.show', $this->training->hash))
            ->line($this->messageText()['closingLine'])
            ->line("If you have any questions or require further information, please don't hesitate to reach out.!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => $this->messageText()['systemNotifText'],
            'redirectRoute' => $this->rediredtRoute()
        ];
    }
}
