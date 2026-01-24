<?php

namespace App\Console\Commands\Trainings;

use App\Notifications\TrainingSchedules\TrainingSchedule;
use App\Notifications\TrainingSchedules\TrainingScheduleReminder;
use App\Repository\Interface\TrainingRepositoryInterface;
use App\Repository\MatchRepository;
use App\Repository\UserRepository;
use App\Services\TrainingService;
use Carbon\Carbon;
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
    private TrainingRepositoryInterface $trainingRepository;
    public function __construct(
        UserRepository  $userRepository,
        TrainingRepositoryInterface $trainingRepository
    )
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->trainingRepository = $trainingRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trainings = $this->trainingRepository->getAll(relations: [], status: ['Scheduled'], startDate: Carbon::now(), endDate: Carbon::now()->addHours(24), reminderNotified:  '0');
        foreach ($trainings as $data) {
            $data->update(['isReminderNotified' => '1']);
            $team = $data->teams()->first();
            $allTeamParticipant = $this->userRepository->allTeamsParticipant($team, admins: false);
            Notification::send($allTeamParticipant, new TrainingSchedule($data, $team, 'reminder'));
        }

        $this->info('Upcoming training schedule successfully sent reminder notification.');
    }
}
