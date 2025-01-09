<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Competition;
use App\Models\LeagueStanding;
use App\Notifications\CompetitionManagements\CompetitionCreatedDeleted;
use App\Notifications\CompetitionManagements\CompetitionStatus;
use App\Notifications\CompetitionManagements\CompetitionUpdated;
use App\Repository\CoachRepository;
use App\Repository\CompetitionRepository;
use App\Repository\Interface\LeagueStandingRepositoryInterface;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class LeagueStandingService extends Service
{
    private LeagueStandingRepositoryInterface $leagueStandingRepository;
    private DatatablesHelper $datatablesService;

    public function __construct(
        LeagueStandingRepositoryInterface $leagueStandingRepository,
        DatatablesHelper $datatablesService
    )
    {
        $this->leagueStandingRepository = $leagueStandingRepository;
        $this->datatablesService = $datatablesService;
    }

    public function index(Competition $competition): JsonResponse
    {
        $query = $this->leagueStandingRepository->getAll($competition);
        return Datatables::of($query)
            ->addColumn('action', function ($item) use ($competition) {

                $edit = '';
                $delete = '';
                if ($competition->status == 'Scheduled' or $competition->status == 'Ongoing') {
                    $edit = $this->datatablesService->buttonDropdownItem('edit-team-standing-btn', $item->id, icon: 'edit', btnText: 'Update Team Standing');
                    $delete = $this->datatablesService->buttonDropdownItem('delete-team', $item->id, iconColor: 'danger', icon: 'delete', btnText: 'Delete Team');
                }

                return '<div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="material-icons">
                                        more_vert
                                    </span>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    '.$edit.'
                                    '.$delete.'
                              </div>
                        </div>';
            })
            ->editColumn('team', function ($item) {
                return $this->datatablesService->name($item->team->logo, $item->team->teamName, $item->team->ageGroup, route('team-managements.show', $item->team->hash));
            })
            ->rawColumns(['action', 'team'])
            ->make();
    }
    public  function store(array $data, Competition $competition)
    {
        return $this->leagueStandingRepository->create($data, $competition);
    }

    public function update(array $data, LeagueStanding $standing)
    {
        return $this->leagueStandingRepository->update($standing, $data);
    }

    public function destroy(LeagueStanding $standing)
    {
        return $this->leagueStandingRepository->delete($standing);
    }
}
