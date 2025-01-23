<?php

namespace App\Console\Commands\Trainings;

use App\Repository\MatchRepository;
use App\Services\MatchService;
use Illuminate\Console\Command;

class CompletedTrainingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:completed-training-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Completed training status records where the end date has passed';
    private MatchService $eventScheduleService;
    private MatchRepository $eventScheduleRepository;

    public function __construct(MatchService $eventScheduleService, MatchRepository $eventScheduleRepository)
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
        // Update records where end_date is less than the current date
        $trainings = $this->eventScheduleRepository->getEndingEvent('Training');
        foreach ($trainings as $training){
            $this->eventScheduleService->setStatus($training, 'Completed');
        }

        $this->info('Training schedule status data updated successfully set to completed.');
    }
}
