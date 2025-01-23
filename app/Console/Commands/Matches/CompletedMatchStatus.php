<?php

namespace App\Console\Commands\Matches;

use App\Repository\MatchRepository;
use App\Services\MatchService;
use Illuminate\Console\Command;

class CompletedMatchStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:completed-match-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Completed match status records where the end date has passed';
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
        $matches = $this->eventScheduleRepository->getAll(relations: [], status: ['Ongoing'], beforeEndDate: true);
        foreach ($matches as $match){
            $this->eventScheduleService->endMatch($match);
        }

        $this->info('Match schedule status data updated successfully set to completed.');
    }
}
