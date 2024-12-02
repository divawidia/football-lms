<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Notifications\Invoices\InvoiceDueSoon;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderAdmin;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderPlayer;
use App\Notifications\TrainingSchedules\TrainingScheduleReminder;
use App\Repository\EventScheduleRepository;
use App\Repository\UserRepository;
use App\Services\EventScheduleService;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class MatchReminderNotification extends Command
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
    private EventScheduleRepository $eventScheduleRepository;
    public function __construct(UserRepository $userRepository,
                                EventScheduleRepository $eventScheduleRepository)
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
        $matches = $this->eventScheduleRepository->getUpcomingEvent('Match', 24);
        foreach ($matches as $data) {
            if ($data->isOpponentTeamMatch == '0') {
                $data->update(['isReminderNotified' => '1']);
                $team = $data->teams()->first();
                $allTeamParticipant = $this->userRepository->allTeamsParticipant($team, admins: false);
                Notification::send($allTeamParticipant, new TrainingScheduleReminder($data, $team));
            }
        }

        $this->info('Upcoming match schedule successfully sent reminder notification.');
    }
}
