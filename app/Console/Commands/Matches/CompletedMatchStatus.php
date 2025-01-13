<?php

namespace App\Console\Commands\Matches;

use App\Repository\EventScheduleRepository;
use App\Services\EventScheduleService;
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
    private EventScheduleService $eventScheduleService;
    private EventScheduleRepository $eventScheduleRepository;

    public function __construct(EventScheduleService $eventScheduleService, EventScheduleRepository $eventScheduleRepository)
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
        $matches = $this->eventScheduleRepository->getEndingEvent('Match');
        foreach ($matches as $match){
            $this->eventScheduleService->endMatch($match);
        }

        $this->info('Match schedule status data updated successfully set to completed.');
    }
}
