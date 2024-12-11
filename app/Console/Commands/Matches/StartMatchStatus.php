<?php

namespace App\Console\Commands\Matches;

use App\Repository\EventScheduleRepository;
use App\Services\EventScheduleService;
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
        $matches = $this->eventScheduleRepository->getScheduledEvent('Match');
        foreach ($matches as $data) {
            $this->eventScheduleService->setStatus($data, 'Ongoing');
        }

        $this->info('Scheduled match schedule successfully set to ongoing.');
    }
}
