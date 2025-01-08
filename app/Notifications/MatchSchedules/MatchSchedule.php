<?php

namespace App\Notifications\MatchSchedules;

use App\Models\EventSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchSchedule extends Notification implements ShouldQueue
{
    use Queueable;
    protected EventSchedule $matchSchedule;
    protected string $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($matchSchedule, $status)
    {
        $this->matchSchedule = $matchSchedule;
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
        $openingLine = "Match session {$this->matchTeams()} scheduled at ".convertToDatetime($this->matchSchedule->startDatetime);
        $closingLine = "Please check the match schedule for more information!";

        if ($this->status == 'create') {
            $subject = "New Match Session Scheduled";
            $openingLine = "A new ".$openingLine;
        } elseif ($this->status == 'delete') {
            $subject = "Match Session Deleted";
            $openingLine = $openingLine." has been deleted.";
        } elseif ($this->status == 'update') {
            $subject = "Match Session Updated";
            $openingLine = $openingLine." has been updated.";
        } elseif ($this->status == 'reminder') {
            $subject = "Match Session Reminder";
            $openingLine = $openingLine." start tomorrow.";
            $closingLine = "Please arrive on time and be prepared for your tomorrow match!";
        } elseif ($this->status == 'ongoing') {
            $subject = "Match Session is Ongoing";
            $openingLine = $openingLine." is now ongoing.";
        } elseif ($this->status == 'complete') {
            $subject = "Match Session Have Been Completed";
            $openingLine = $openingLine." have been completed.";
        } elseif ($this->status == 'cancel') {
            $subject = "Match Session Have Been Cancelled";
            $openingLine = $openingLine." have been cancelled.";
        } elseif ($this->status == 'scheduled') {
            $subject = "Match Session Have Been Set to Scheduled";
            $openingLine = $openingLine." have been set to scheduled.";
        }
        $systemNotifText = $openingLine.". ".$closingLine;

        return compact('subject', 'openingLine', 'closingLine', 'systemNotifText');
    }

    private function rediredtRoute()
    {
        if ($this->status == 'delete') {
            return route('match-schedules.index');
        } else {
            return route('match-schedules.show', $this->matchSchedule->hash);
        }
    }

    private function matchTeams()
    {
        if ($this->matchSchedule->matchType == 'Internal Match') {
            return $this->matchSchedule->teams[0]->teamName. " Vs. ". $this->matchSchedule->teams[1]->teamName;
        } else {
            return $this->matchSchedule->teams[0]->teamName. " Vs. ". $this->matchSchedule->externalTeam->teamName;
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->messageText()['subject'])
            ->greeting("Hello {$notifiable->firstName} {$notifiable->lastName}!")
            ->line($this->messageText()['openingLine'])
            ->line("Match Teams: {$this->matchTeams()}")
            ->line("Match Type: {$this->matchSchedule->matchType}")
            ->line("Location: {$this->matchSchedule->place}")
            ->line("Date: ".convertToDate($this->matchSchedule->date))
            ->line("Start Time: ".convertToTime($this->matchSchedule->startTime))
            ->line("End Time: ".convertToTime($this->matchSchedule->endTime))
            ->action('View match session detail', $this->rediredtRoute())
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
