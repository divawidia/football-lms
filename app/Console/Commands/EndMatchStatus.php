<?php

namespace App\Console\Commands;

use App\Models\EventSchedule;
use App\Services\EventScheduleService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EndMatchStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:end-match-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ended match status records where the end date has passed';
    private EventScheduleService $eventScheduleService;

    public function __construct(EventScheduleService $eventScheduleService)
    {
        parent::__construct();
        $this->eventScheduleService = $eventScheduleService;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current date and time
        $now = Carbon::now();

        // Update records where end_date is less than the current date
        $matches = EventSchedule::where('eventType', 'Match')->where('endDatetime', '<', $now)->get();
        foreach ($matches as $match){
            $this->eventScheduleService->endMatch($match);
        }

        $this->info('Active status training data updated successfully.');
    }
}
