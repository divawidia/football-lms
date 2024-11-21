<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\Player;
use App\Repository\EventScheduleRepository;
use App\Repository\PlayerRepository;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AttendanceReportService extends Service
{
    private PlayerRepository $playerRepository;
    private EventScheduleRepository $eventScheduleRepository;
    private DatatablesService $datatablesService;
    public function __construct(PlayerRepository $playerRepository, EventScheduleRepository $eventScheduleRepository, DatatablesService $datatablesService)
    {
        $this->playerRepository = $playerRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
        $this->datatablesService = $datatablesService;
    }

    public function makeAttendanceDatatables($data)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesService->buttonTooltips(route('attendance-report.show', $item->id), 'View player attendance detail', 'visibility');
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
                return $this->datatablesService->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name);
            })
            ->addColumn('totalEvent', function ($item){
                return count($item->schedules);
            })
            ->addColumn('match', function ($item){
                return $item->schedules()->where('eventType', 'Match')->count();
            })
            ->addColumn('training', function ($item){
                return $item->schedules()->where('eventType', 'Training')->count();
            })
            ->addColumn('attended', function ($item){
                $attended = $item->schedules()->where('attendanceStatus', 'Attended')->get();
                $totalAttended = count($attended);
                $totalEvent = count($item->schedules);
                if ($totalEvent == 0){
                    return 'No event yet';
                }else{
                    $percentage = $totalAttended/count($item->schedules)*100;
                    return $totalAttended . ' ('.round($percentage, 1).'%)';
                }
            })
            ->addColumn('absent', function ($item){
                $illness = $item->schedules()
                    ->where('attendanceStatus', 'Illness')
                    ->get();
                $injured = $item->schedules()
                    ->where('attendanceStatus', 'Injured')
                    ->get();
                $others = $item->schedules()
                    ->where('attendanceStatus', 'Others')
                    ->get();

                $totalDidntAttended = count($illness) + count($injured) + count($others);
                $totalEvent = count($item->schedules);
                if ($totalEvent == 0){
                    return 'No event yet';
                }else{
                    $percentage = $totalDidntAttended/count($item->schedules)*100;
                    return $totalDidntAttended . ' ('.round($percentage, 1).'%)';
                }
            })
            ->addColumn('illness', function ($item){
                $didntAttend = $item->schedules()
                    ->where('attendanceStatus', 'Illness')
                    ->get();
                return count($didntAttend);
            })
            ->addColumn('injured', function ($item){
                $didntAttend = $item->schedules()
                    ->where('attendanceStatus', 'Injured')
                    ->get();
                return count($didntAttend);
            })
            ->addColumn('others', function ($item){
                $didntAttend = $item->schedules()
                    ->where('attendanceStatus', 'Others')
                    ->get();
                return count($didntAttend);
            })
            ->rawColumns(['action','teams', 'name','totalEvent', 'match', 'training', 'attended', 'absent', 'illness', 'injured', 'others'])
            ->make();
    }
    public function attendanceDatatables(){
        $query = $this->playerRepository->getAll();
        return $this->makeAttendanceDatatables($query);
    }
    public function coachAttendanceDatatables($coach){
        $teams = $coach->teams()->get();

        // query player data that included in teams that managed by logged in coach
        $query = $this->playerRepository->getPLayersByTeams($teams);
        return $this->makeAttendanceDatatables($query);
    }

    public function index(){
        $mostAttended = $this->playerRepository->getMostAttendedPLayer();

        $mostAttendedPercentage = $mostAttended->attended_count / count($mostAttended->schedules) * 100;
        $mostAttendedPercentage = round($mostAttendedPercentage, 1);

        $mostDidntAttend = $this->playerRepository->getMostDidntAttendPLayer();

        $mostDidntAttendPercentage = $mostDidntAttend->didnt_attended_count / count($mostDidntAttend->schedules) * 100;
        $mostDidntAttendPercentage = round($mostDidntAttendPercentage, 1);

        $lineChart = $this->attendanceLineChart();
        $doughnutChart = $this->attendanceDoughnutChart();

        return compact('mostAttended', 'mostDidntAttend', 'mostAttendedPercentage', 'mostDidntAttendPercentage', 'lineChart', 'doughnutChart');
    }
    public function coachIndex($coach){
        $teams = $coach->teams;

        $mostAttended = $this->playerRepository->getMostAttendedCoachsPLayer($teams);

        $mostAttendedPercentage = $mostAttended['attended_count'] / count($mostAttended->schedules) * 100;
        $mostAttendedPercentage = round($mostAttendedPercentage, 1);

        $mostDidntAttend = $this->playerRepository->getMostDidntAttendCoachsPLayer($teams);

        $mostDidntAttendPercentage = $mostDidntAttend['didnt_attended_count'] / count($mostDidntAttend->schedules) * 100;
        $mostDidntAttendPercentage = round($mostDidntAttendPercentage, 1);

        return compact('mostAttended', 'mostDidntAttend', 'mostAttendedPercentage', 'mostDidntAttendPercentage');
    }

    public function attendanceLineChart(Player $player = null, Coach $coach = null)
    {
        $attendedData = $this->getAttendanceData($player, status: 'Attended');
        $attendanceData = $this->getAttendanceData($player);
        $didntAttendData = $this->getAttendanceData($player, status: 'didntAttended');
        $attendanceDate = $this->getAttendanceData($player, isGetDateOnly: true);

        $labels = [];
        $attended = [];
        $didntAttend = [];

        foreach ($attendanceData as $result) {
            $labels[] = $result->date;
            $attended[] = $result->total_attended_players;
            $didntAttend[] = $result->total_didnt_attend_players;
        }
//        foreach ($attendedData as $result) {
//            $attended[] = $result->total_attended_players;
//        }
//        foreach ($didntAttendData as $result) {
//            $didntAttend[] = $result->total_didnt_attend_players;
//        }

        return compact('labels', 'attended', 'didntAttend');
    }

    private function getAttendanceData(Player $player = null, Coach $coach = null, $status = null, $isGetDateOnly = false)
    {
        $query = DB::table('event_schedules as es')
            ->join('player_attendance as pa', 'es.id', '=', 'pa.scheduleId')
            ->join('players as p', 'pa.playerId', '=', 'p.id');

        if ($coach) {
            $teams = $coach->teams;
            $teamIds = collect($teams)->pluck('id')->all();
            $query->join('player_teams', function (JoinClause $join) use ($teamIds) {
                $join->on('p.id', '=', 'player_teams.playerId')
                    ->whereIn('teamId', $teamIds);
            });
        }
        $query->select(
            DB::raw('es.date as date'),
            DB::raw("COUNT(CASE WHEN pa.attendanceStatus = 'Attended' THEN 1 END) AS total_attended_players"),
            DB::raw("COUNT(CASE WHEN pa.attendanceStatus != 'Attended' THEN 1 END) AS total_didnt_attend_players"));

//        if ($status == 'Attended') {
//            $query->select(DB::raw('es.date as date'), DB::raw('COUNT(pa.playerId) as total_attended_players'))
//                ->where('pa.attendanceStatus', '=', 'Attended');
//        } elseif ($status == 'didntAttended'){
//            $query->select(DB::raw('es.date as date'), DB::raw('COUNT(pa.playerId) as total_didnt_attend_players'))
//                ->where(DB::raw("pa.attendanceStatus = 'Illness' OR pa.attendanceStatus = 'Injured' OR pa.attendanceStatus = 'Other'"));
//        }
//
//        if ($isGetDateOnly == true){
//            $query->select(DB::raw('es.date as date'));
//        }
        $query->where('es.status', '0');
        if ($player) {
            $query->where('p.id', $player->id);
        }

        return $query->groupBy(DB::raw('date'))
            ->orderBy('date')
            ->get();
    }

    public function attendanceDoughnutChart(Player $player = null, Coach $coach = null){
        $query = DB::table('event_schedules as es')
            ->join('player_attendance as pa', 'es.id', '=', 'pa.scheduleId')
            ->join('players as p', 'pa.playerId', '=', 'p.id');

        if ($coach){
            $teams = $coach->teams;
            $teamIds = collect($teams)->pluck('id')->all();
            $query->join('player_teams', function (JoinClause $join) use ($teamIds) {
                $join->on('p.id', '=', 'player_teams.playerId')
                    ->whereIn('teamId', $teamIds);
            });
        }

        $query->select(DB::raw('pa.attendanceStatus as status'), DB::raw('COUNT(pa.playerId) AS total_players'))
            ->where('pa.attendanceStatus', '!=', 'Required Action');

        if ($player) {
            $query->where('p.id', $player->id);
        }
        $query->groupBy(DB::raw('pa.attendanceStatus'));

        $query = $query->get();
        $label = [];
        $data = [];
        foreach ($query as $result){
            $label[] = $result->status;
            $data[] = $result->total_players;
        }

        return compact('label', 'data');
    }

    public function show(Player $player){
        $totalAttended = $this->playerRepository->playerAttendanceCount($player);
        $thisMonthTotalAttended = $this->playerRepository->playerAttendanceCount($player, 'Attended', Carbon::now()->startOfMonth(), Carbon::now());

        $totalIllness = $this->playerRepository->playerAttendanceCount($player, 'Illness');
        $thisMonthTotalIllness = $this->playerRepository->playerAttendanceCount($player, 'Illness', Carbon::now()->startOfMonth(), Carbon::now());

        $totalInjured = $this->playerRepository->playerAttendanceCount($player, 'Injured');
        $thisMonthTotalInjured = $this->playerRepository->playerAttendanceCount($player, 'Injured', Carbon::now()->startOfMonth(), Carbon::now());

        $totalOther = $this->playerRepository->playerAttendanceCount($player, 'Other');
        $thisMonthTotalOther = $this->playerRepository->playerAttendanceCount($player, 'Other', Carbon::now()->startOfMonth(), Carbon::now());

        $lineChart = $this->attendanceLineChart($player);
        $doughnutChart = $this->attendanceDoughnutChart($player);

        return compact(
            'totalAttended',
            'thisMonthTotalAttended',
            'totalIllness',
            'thisMonthTotalIllness',
            'totalInjured',
            'thisMonthTotalInjured',
            'totalOther',
            'thisMonthTotalOther',
            'lineChart',
            'doughnutChart',
            'player'
        );
    }

    public function dataTablesTraining(Player $player){
        $data = $this->eventScheduleRepository->getEventByModel($player, 'Training', 'Completed');
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesService->buttonTooltips(route('training-schedules.show', $item->id), 'View training session', 'visibility');
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
    public function dataTablesMatch(Player $player){
        $data = $this->eventScheduleRepository->getEventByModel($player, 'Match', 'Completed');
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesService->buttonTooltips(route('match-schedules.show', $item->id), 'View match session', 'visibility');
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
