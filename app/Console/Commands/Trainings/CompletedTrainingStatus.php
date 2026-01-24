<?php

namespace App\Console\Commands\Trainings;

use App\Repository\Interface\TrainingRepositoryInterface;
use App\Repository\MatchRepository;
use App\Services\MatchService;
use App\Services\TrainingService;
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
    private TrainingService $trainingService;
    private TrainingRepositoryInterface $trainingRepository;
    public function __construct(
        TrainingService $trainingService,
        TrainingRepositoryInterface $trainingRepository)
    {
        parent::__construct();
        $this->trainingService = $trainingService;
        $this->trainingRepository = $trainingRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Update records where end_date is less than the current date
        $trainings = $this->trainingRepository->getAll(relations: [], status: ['Ongoing'], beforeEndDate: true);
        foreach ($trainings as $training){
            $this->trainingService->setStatus($training, 'Completed');
        }

        $this->info('Training schedule status data updated successfully set to completed.');
    }
}
