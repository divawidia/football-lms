<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Player;
use App\Repository\EventScheduleRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class AttendanceReportService extends Service
{
    private PlayerRepository $playerRepository;
    private EventScheduleRepository $eventScheduleRepository;
    private DatatablesHelper $datatablesService;
    private TeamRepository $teamRepository;
    public function __construct(
        PlayerRepository        $playerRepository,
        EventScheduleRepository $eventScheduleRepository,
        DatatablesHelper        $datatablesService,
        TeamRepository          $teamRepository
    )
    {
        $this->playerRepository = $playerRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->datatablesService = $datatablesService;
        $this->teamRepository = $teamRepository;
    }

    public function makeAttendanceDatatables($data, $startDate, $endDate, $eventType = null): JsonResponse
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesService->buttonTooltips(route('attendance-report.show', $item->hash), 'View player attendance detail', 'visibility');
            })
            ->editColumn('teams', function ($item) {
                $playerTeam = '';
                if(count($item->teams) === 0){
                    $playerTeam = 'No Team';
                }else{
                    foreach ($item->teams as $team){
                        $playerTeam .= '<span class="badge badge-pill badge-danger">'.$team->teamName.'</span>';
                    }
                }
                return $playerTeam;
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->addColumn('totalEvent', function ($item) use ($startDate, $endDate, $eventType){
                return $this->eventScheduleRepository->playerAttendance($item, null, $startDate, $endDate, $eventType);
            })
            ->addColumn('match', function ($item) use ($startDate, $endDate, $eventType) {
                if ($eventType == 'Training') {
                    $data = 0;
                } else {
                    $data = $this->eventScheduleRepository->playerAttendance($item, null, $startDate, $endDate, 'Match');
                }
                return $data;
            })
            ->addColumn('training', function ($item) use ($startDate, $endDate, $eventType) {
                if ($eventType == 'Match') {
                    $data = 0;
                } else {
                    $data = $this->eventScheduleRepository->playerAttendance($item, null, $startDate, $endDate, 'Training');
                }
                return $data;
            })
            ->addColumn('attended', function ($item) use ($startDate, $endDate, $eventType) {
                $attended = $this->eventScheduleRepository->playerAttendance($item, 'Attended', $startDate, $endDate, $eventType);
                $totalEvent = $this->eventScheduleRepository->playerAttendance($item, null, $startDate, $endDate, $eventType);
                if ($totalEvent == 0){
                    return 'No event yet';
                }else{
                    $percentage = $attended/$totalEvent*100;
                    return $attended . ' ('.round($percentage, 1).'%)';
                }
            })
            ->addColumn('absent', function ($item) use ($startDate, $endDate, $eventType) {
                $illness = $this->eventScheduleRepository->playerAttendance($item, 'Illness', $startDate, $endDate, $eventType);
                $injured = $this->eventScheduleRepository->playerAttendance($item, 'Injured', $startDate, $endDate, $eventType);
                $others = $this->eventScheduleRepository->playerAttendance($item, 'Others', $startDate, $endDate, $eventType);

                $totalDidntAttended = $illness + $injured + $others;
                $totalEvent = $this->eventScheduleRepository->playerAttendance($item, null, $startDate, $endDate, $eventType);
                if ($totalEvent == 0){
                    return 'No event yet';
                }else{
                    $percentage = $totalDidntAttended/$totalEvent*100;
                    return $totalDidntAttended . ' ('.round($percentage, 1).'%)';
                }
            })
            ->addColumn('illness', function ($item) use ($startDate, $endDate, $eventType) {
                return  $this->eventScheduleRepository->playerAttendance($item, 'Illness', $startDate, $endDate, $eventType);
            })
            ->addColumn('injured', function ($item) use ($startDate, $endDate, $eventType) {
                return $this->eventScheduleRepository->playerAttendance($item, 'Injured', $startDate, $endDate, $eventType);
            })
            ->addColumn('others', function ($item) use ($startDate, $endDate, $eventType) {
                return $this->eventScheduleRepository->playerAttendance($item, 'Others', $startDate, $endDate, $eventType);
            })
            ->addColumn('requiredAction', function ($item) use ($startDate, $endDate, $eventType) {
                return $this->eventScheduleRepository->playerAttendance($item, 'Required Action', $startDate, $endDate, $eventType);
            })
            ->rawColumns(['action','teams', 'name','totalEvent', 'match', 'training', 'attended', 'absent', 'illness', 'injured', 'others', 'requiredAction'])
            ->make();
    }
    public function attendanceDatatables($teams = null, $startDate, $endDate, $eventType = null): JsonResponse
    {
        if ($teams) {
            $teams = $this->teamRepository->whereId($teams);
            $query = $this->playerRepository->getPLayersByTeams($teams);
        } else {
            $query = $this->playerRepository->getAll();
        }
        $filter = $this->dateFilter($startDate, $endDate);
        return $this->makeAttendanceDatatables($query, $filter['startDate'], $filter['endDate'], $eventType);
    }
    public function coachAttendanceDatatables($coach, $teams = null, $startDate, $endDate, $eventType = null): JsonResponse
    {
        if ($teams) {
            $teams = $this->teamRepository->whereId($teams);
            $query = $this->playerRepository->getPLayersByTeams($teams);
        } else {
            $teams = $coach->teams;
            // query player data that included in teams that managed by logged in coach
            $query = $this->playerRepository->getPLayersByTeams($teams);
        }
        $filter = $this->dateFilter($startDate, $endDate);
        return $this->makeAttendanceDatatables($query, $filter['startDate'], $filter['endDate'], $eventType);
    }

    public function eventIndex($startDate, $endDate, $teams = null, $eventType = null): JsonResponse
    {
        if (is_string($teams)) {
            $teams = $this->teamRepository->whereId($teams);
        }
        $filter = $this->dateFilter($startDate, $endDate);
        $data = $this->eventScheduleRepository->getEvent('Completed', $eventType, null, $filter['startDate'], $filter['endDate'], $teams);
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if ($item->eventType == 'Training') {
                    $btn = $this->datatablesService->buttonTooltips(route('training-schedules.show', $item->hash), 'View training session', 'visibility');
                } else {
                    $btn = $this->datatablesService->buttonTooltips(route('match-schedules.show', $item->hash), 'View match session', 'visibility');
                }
                return $btn;
            })
            ->addColumn('team', function ($item) {
                return $this->datatablesService->name($item->teams[0]->logo, $item->teams[0]->teamName, $item->teams[0]->ageGroup, route('team-managements.show', $item->teams[0]->hash));
            })
            ->editColumn('eventName', function ($item) {
                if ($item->eventType == 'Training') {
                    $data = $item->eventName;
                } else {
                    $data = $item->teams[0]->teamName.' Vs. '.$item->teams[1]->teamName;
                }
                return $data;
            })
            ->addColumn('totalPlayers', function ($item) {
                return count($item->players);
            })
            ->addColumn('playerAttended', function ($item) {
                return $this->eventScheduleRepository->playerAttendanceCount('Attended', $item->id);
            })
            ->addColumn('playerIllness', function ($item) {
                return $this->eventScheduleRepository->playerAttendanceCount('Illness', $item->id);
            })
            ->addColumn('playerInjured', function ($item) {
                return $this->eventScheduleRepository->playerAttendanceCount('Injured', $item->id);
            })
            ->addColumn('playerOther', function ($item) {
                return $this->eventScheduleRepository->playerAttendanceCount('Other', $item->id);
            })
            ->addColumn('playerRequiredAction', function ($item) {
                return $this->eventScheduleRepository->playerAttendanceCount('Required Action', $item->id);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->eventStatus($item->status);
            })
            ->rawColumns([
                'action',
                'team',
                'eventName',
                'totalPlayers',
                'playerAttended',
                'playerIllness',
                'playerInjured',
                'playerOther',
                'playerRequiredAction',
                'status'
            ])
            ->make();
    }

    public function mostAttendedPlayer($startDate, $endDate, $teams = null, $eventType = null): array
    {
        $filter = $this->dateFilter($startDate, $endDate);
        $results = $this->playerRepository->getAttendedPLayer($filter['startDate'], $filter['endDate'], $teams, $eventType);
        if ($results != null and $results->schedules_count > 0) {
            $attended_count = $results->attended_count;
            $schedules_count = $results->schedules_count;
            $mostAttendedPercentage = round($attended_count / $schedules_count * 100, 1);
        } else {
            $mostAttendedPercentage = null;
        }

        return compact('results', 'mostAttendedPercentage');
    }

    public function mostDidntAttendPlayer($startDate, $endDate, $teams = null, $eventType = null): array
    {
        $filter = $this->dateFilter($startDate, $endDate);
        $results = $this->playerRepository->getAttendedPLayer($filter['startDate'], $filter['endDate'], $teams, $eventType, mostAttended: false, mostDidntAttend: true);
        if ($results != null and $results->schedules_count > 0) {
            $didnt_attended_count = $results->didnt_attended_count;
            $schedules_count = $results->schedules_count;
            $mostDidntAttendPercentage = round($didnt_attended_count / $schedules_count * 100, 1);
        } else {
            $mostDidntAttendPercentage = null;
        }
        return compact('results', 'mostDidntAttendPercentage');
    }

    public function dateFilter($startDate, $endDate): array
    {
        if ($startDate == null) {
            $startDate = Carbon::now()->startOfYear();
        }
        if ($endDate == null) {
            $endDate = Carbon::now();
        }
        return compact('startDate', 'endDate');
    }

    public function attendanceLineChart($startDate, $endDate,  $teams = null, $eventType = null)
    {
        $filter = $this->dateFilter($startDate, $endDate);
        $attendanceData = $this->eventScheduleRepository->getAttendanceTrend($filter['startDate'], $filter['endDate'], $teams, $eventType);
        return [
            'labels' => $attendanceData->pluck('date'),
            'datasets' => [
                [
                    'label' => 'Total number of attended players',
                    'data' => $attendanceData->pluck('total_attended_players'),
                    'borderColor' => '#20F4CB',
                    'tension' => 0.4,
                ],[
                    'label' => 'Total number of ill players',
                    'data' => $attendanceData->pluck('total_of_ill_players'),
                    'borderColor' => '#E52534',
                    'tension' => 0.4,
                ],[
                    'label' => 'Total number of injured players',
                    'data' => $attendanceData->pluck('total_of_injured_players'),
                    'borderColor' => '#F9B300',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Total number of other status players',
                    'data' => $attendanceData->pluck('total_of_other_attendance_status_players'),
                    'borderColor' => '#00122A',
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    public function attendanceDoughnutChart($startDate, $endDate, $teams = null, $eventType = null): array
    {
        $filter = $this->dateFilter($startDate, $endDate);
        $result = $this->eventScheduleRepository->countAttendanceByStatus($filter['startDate'], $filter['endDate'], $teams, $eventType);

        return [
            'labels' => $result->pluck('status'),
            'datasets' => [
                [
                    'label' => 'Total number of players',
                    'data' => $result->pluck('total_players'),
                    'backgroundColor' => ['#20F4CB', '#E52534', '#F9B300', '#00122A'],
                    'tension' => 0.4,
                ]
            ],
        ];
    }

    public function show(Player $player): array
    {
        $totalAttended = $this->playerRepository->playerAttendanceCount($player);
        $thisMonthTotalAttended = $this->playerRepository->playerAttendanceCount($player, 'Attended', Carbon::now()->startOfMonth(), Carbon::now());

        $totalIllness = $this->playerRepository->playerAttendanceCount($player, 'Illness');
        $thisMonthTotalIllness = $this->playerRepository->playerAttendanceCount($player, 'Illness', Carbon::now()->startOfMonth(), Carbon::now());

        $totalInjured = $this->playerRepository->playerAttendanceCount($player, 'Injured');
        $thisMonthTotalInjured = $this->playerRepository->playerAttendanceCount($player, 'Injured', Carbon::now()->startOfMonth(), Carbon::now());

        $totalOther = $this->playerRepository->playerAttendanceCount($player, 'Other');
        $thisMonthTotalOther = $this->playerRepository->playerAttendanceCount($player, 'Other', Carbon::now()->startOfMonth(), Carbon::now());

//        $lineChart = $this->attendanceLineChart($player);
//        $doughnutChart = $this->attendanceDoughnutChart($player);

        return compact(
            'totalAttended',
            'thisMonthTotalAttended',
            'totalIllness',
            'thisMonthTotalIllness',
            'totalInjured',
            'thisMonthTotalInjured',
            'totalOther',
            'thisMonthTotalOther',
//            'lineChart',
//            'doughnutChart',
            'player'
        );
    }

    public function dataTablesTraining(Player $player): JsonResponse
    {
        $data = $this->eventScheduleRepository->getEventByModel($player, 'Training', 'Completed');
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesService->buttonTooltips(route('training-schedules.show', $item->hash), 'View training session', 'visibility');
            })
            ->editColumn('team', function ($item) {
                return $this->datatablesService->name($item->teams[0]->logo, $item->teams[0]->teamName, $item->teams[0]->ageGroup);
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesService->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->eventStatus($item->status);
            })
            ->editColumn('attendanceStatus', function ($item) {
                return $this->datatablesService->attendanceStatus($item->pivot->attendanceStatus);
            })
            ->editColumn('note', function ($item) {
                if ($item->pivot->note == null) {
                    return 'No note added';
                } else {
                    return $item->pivot->note;
                }
            })
            ->editColumn('last_updated', function ($item) {
                return $this->convertToDatetime($item->pivot->updated_at);
            })
            ->rawColumns(['action','team','date','status', 'attendanceStatus', 'last_updated', 'note'])
            ->addIndexColumn()
            ->make();
    }
    public function dataTablesMatch(Player $player): JsonResponse
    {
        $data = $this->eventScheduleRepository->getEventByModel($player, 'Match', 'Completed');
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesService->buttonTooltips(route('match-schedules.show', $item->hash), 'View match session', 'visibility');
            })
            ->editColumn('team', function ($item) {
                return $this->datatablesService->name($item->teams[0]->logo, $item->teams[0]->teamName, $item->teams[0]->ageGroup);
            })
            ->editColumn('opponentTeam', function ($item) {
                return $this->datatablesService->name($item->teams[1]->logo, $item->teams[1]->teamName, $item->teams[1]->ageGroup);
            })
            ->editColumn('competition', function ($item) {
                if ($item->competition){
                    $competition = $this->datatablesService->name($item->competition->logo, $item->competition->name, $item->competition->type);
                }else{
                    $competition = 'No Competition';
                }
                return $competition;
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesService->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesService->eventStatus($item->status);
            })
            ->editColumn('attendanceStatus', function ($item) {
                return $this->datatablesService->attendanceStatus($item->pivot->attendanceStatus);
            })
            ->editColumn('note', function ($item) {
                if ($item->pivot->note == null) {
                    return 'No note added';
                } else {
                    return $item->pivot->note;
                }
            })
            ->editColumn('last_updated', function ($item) {
                return $this->convertToDatetime($item->pivot->updated_at);
            })
            ->rawColumns(['action','team', 'competition','opponentTeam','date','status', 'attendanceStatus', 'last_updated', 'note'])
            ->addIndexColumn()
            ->make();
    }
}
