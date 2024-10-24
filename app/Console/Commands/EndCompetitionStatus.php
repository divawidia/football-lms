<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\EventSchedule;
use App\Services\CompetitionService;
use App\Services\EventScheduleService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EndCompetitionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:end-competition-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ended competition status records where the end date has passed';
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
        $competitions = Competition::where('endDate', '<', $now)->get();
        foreach ($competitions as $competition){
            $this->competitionService->deactivate($competition);
        }

        $this->info('Competition status data updated successfully.');
    }
}
