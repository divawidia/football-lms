<?php

namespace App\Console\Commands\Trainings;

use App\Notifications\TrainingSchedules\TrainingScheduleReminder;
use App\Repository\MatchRepository;
use App\Repository\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class TrainingReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:training-reminder-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent training schedule reminder notification to players, coaches';

    private UserRepository $userRepository;
    private MatchRepository $eventScheduleRepository;
    public function __construct(UserRepository  $userRepository,
                                MatchRepository $eventScheduleRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trainings = $this->eventScheduleRepository->getUpcomingEvent('Training', 24);
        foreach ($trainings as $data) {
            $data->update(['isReminderNotified' => '1']);
            $team = $data->teams()->first();
            $allTeamParticipant = $this->userRepository->allTeamsParticipant($team, admins: false);
            Notification::send($allTeamParticipant, new TrainingScheduleReminder($data, $team));
        }

        $this->info('Upcoming training schedule successfully sent reminder notification.');
    }
}
