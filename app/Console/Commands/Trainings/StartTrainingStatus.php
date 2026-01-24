<?php

namespace App\Console\Commands\Trainings;

use App\Repository\Interface\TrainingRepositoryInterface;
use App\Repository\MatchRepository;
use App\Services\MatchService;
use App\Services\TrainingService;
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
        $trainings = $this->trainingRepository->getAll(relations: [], status: ['Scheduled'], beforeStartDate: true);
        foreach ($trainings as $data) {
            $this->trainingService->setStatus($data, 'Ongoing');
        }

        $this->info('Scheduled training schedule successfully set to ongoing.');
    }
}
