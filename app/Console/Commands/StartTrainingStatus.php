<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Notifications\Invoices\InvoiceDueSoon;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderAdmin;
use App\Notifications\Subscriptions\SubscriptionDueDateReminderPlayer;
use App\Notifications\TrainingSchedules\TrainingScheduleReminder;
use App\Notifications\TrainingSchedules\TrainingScheduleUpdatedForCoachAdmin;
use App\Repository\EventScheduleRepository;
use App\Repository\UserRepository;
use App\Services\EventScheduleService;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class StartTrainingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:start-training-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set training status records to ongoing where the start date has passed';

    private EventScheduleService $eventScheduleService;
    private EventScheduleRepository $eventScheduleRepository;
    public function __construct(EventScheduleService $eventScheduleService,
                                EventScheduleRepository $eventScheduleRepository)
    {
        parent::__construct();
        $this->eventScheduleService = $eventScheduleService;
        $this->eventScheduleRepository = $eventScheduleRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trainings = $this->eventScheduleRepository->getUpcomingEvent('Training', 0);
        foreach ($trainings as $data) {
            $this->eventScheduleService->setStatus($data, 'Ongoing');
        }

        $this->info('Scheduled training schedule successfully set to ongoing.');
    }
}