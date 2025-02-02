<?php

namespace App\Console\Commands\Matches;

use App\Notifications\MatchSchedules\MatchSchedule;
use App\Notifications\MatchSchedules\MatchScheduleReminder;
use App\Repository\MatchRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class MatchReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:match-reminder-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent match schedule reminder notification to players, coaches';

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
        $matches = $this->eventScheduleRepository->getAll(relations: [], status: ['Scheduled'], startDate: Carbon::now(), endDate: Carbon::now()->addHours(24), reminderNotified:  '0');
        foreach ($matches as $data) {
            $data->update(['isReminderNotified' => '1']);
            $homeTeamParticipant = $this->userRepository->allTeamsParticipant($data->homeTeam);
            Notification::send($homeTeamParticipant, new MatchSchedule($data, 'reminder'));

            if ($data->matchType = 'Internal Match') {
                $awayTeamParticipant = $this->userRepository->allTeamsParticipant($data->awayTeam);
                Notification::send($awayTeamParticipant, new MatchSchedule($data, 'reminder'));
            }
        }

        $this->info('Upcoming match schedule successfully sent reminder notification.');
    }
}
