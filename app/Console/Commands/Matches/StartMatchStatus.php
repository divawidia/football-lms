<?php

namespace App\Console\Commands\Matches;

use App\Repository\MatchRepository;
use App\Services\MatchService;
use Illuminate\Console\Command;

class StartMatchStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:start-match-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set match schedule status records to ongoing where the start date has passed';

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
        $matches = $this->eventScheduleRepository->getAll(relations: [], status: ['Scheduled'], beforeStartDate: true);
        foreach ($matches as $data) {
            $this->eventScheduleService->setStatus($data, 'Ongoing');
        }

        $this->info('Scheduled match schedule successfully set to ongoing.');
    }
}
