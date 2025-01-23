<?php

namespace App\Console\Commands\Trainings;

use App\Repository\MatchRepository;
use App\Services\MatchService;
use Illuminate\Console\Command;

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

    private MatchService $eventScheduleService;
    private MatchRepository $eventScheduleRepository;
    public function __construct(MatchService    $eventScheduleService,
                                MatchRepository $eventScheduleRepository)
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
        $trainings = $this->eventScheduleRepository->getScheduledEvent('Training');
        foreach ($trainings as $data) {
            $this->eventScheduleService->setStatus($data, 'Ongoing');
        }

        $this->info('Scheduled training schedule successfully set to ongoing.');
    }
}
