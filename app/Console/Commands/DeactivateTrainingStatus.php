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
    protected $signature = 'app:deactivate-training-status';

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
        EventSchedule::where('end_date', '<', $now)
            ->update(['status' => 'expired']);  // Modify the fields you want to update

        $this->info('Expired data updated successfully.');
    }
}
