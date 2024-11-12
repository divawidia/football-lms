<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\EventSchedule;
use App\Services\CompetitionService;
use App\Services\EventScheduleService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompletedCompetitionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:complete-competition-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Completed/Ended competition status records where the end date has passed';
    private CompetitionService $competitionService;

    public function __construct(CompetitionService $competitionService)
    {
        parent::__construct();
        $this->competitionService = $competitionService;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current date and time
        $now = Carbon::now();

        // Update records where end_date is less than the current date
        $competitions = Competition::where('endDate', '<=', $now)->where('status', '!=', 'Cancelled')->get();
        foreach ($competitions as $competition){
            $this->competitionService->setStatus($competition, 'Completed');
            $this->info('Competition '.$competition->name.' status data successfully updated to completed.');
        }


    }
}
