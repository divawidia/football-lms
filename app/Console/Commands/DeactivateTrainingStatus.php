<?php

namespace App\Console\Commands;

use App\Models\EventSchedule;
use App\Services\EventScheduleService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeactivateTrainingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:training-status-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate training status records where the end date has passed';
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
        $trainings = EventSchedule::where('eventType', 'Training')->where('endDatetime', '<', $now)->get();
        foreach ($trainings as $training){
            $this->eventScheduleService->deactivate($training);
        }

        $this->info('Active status training data updated successfully.');
    }
}
