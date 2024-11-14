<?php

namespace App\Notifications\MatchSchedules;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchScheduleAttendance extends Notification
{
    use Queueable;
    protected $scheduleDetails;
    protected $status;

    public function __construct($scheduleDetails, $status)
    {
        $this->scheduleDetails = $scheduleDetails;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Match Session Attendance Notification")
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line("This is a notification about your attendance for the Match session for your team {$this->scheduleDetails->teams[0]->teamName} at ".convertToDatetime($this->scheduleDetails->startDatetime).".")
            ->line("Match Teams: {$this->scheduleDetails->teams[0]->teamName} Vs. {$this->scheduleDetails->teams[1]->teamName}")
            ->line("Match Type: {$this->scheduleDetails->matchType}")
            ->line("Location: {$this->scheduleDetails->place}")
            ->line("Date: ".convertToDate($this->scheduleDetails->date))
            ->line("Start Time: ".convertToTime($this->scheduleDetails->startTime))
            ->line("End Time: ".convertToTime($this->scheduleDetails->endTime))
            ->line("Attendance Status: {$this->status}.")
            ->action('View match session detail', route('match-schedules.show', $this->scheduleDetails->id))
            ->line("Please ensure your attendance is marked accordingly.")
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
            'data' =>'Your attendance for the match session for your team '.$this->scheduleDetails->teams[0]->teamName.' at '.convertToDatetime($this->scheduleDetails->startDatetime).' is marked as: '.$this->status,
            'redirectRoute' => route('match-schedules.show', $this->scheduleDetails->id)
        ];
    }
}
