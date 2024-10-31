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
    public function __construct(PlayerRepository $playerRepository, EventScheduleRepository $eventScheduleRepository)
    {
        $this->playerRepository = $playerRepository;
        $this->eventScheduleRepository = $eventScheduleRepository;
    }

    public function makeAttendanceDatatables($data)
    {
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                    $viewButton = '
                        <a class="btn btn-sm btn-outline-secondary" href="' . route('attendance-report.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View player attendance detail">
                            <span class="material-icons">visibility</span>
                        </a>';
                return $viewButton;
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
                return '
                        <div class="media flex-nowrap align-items-center"
                             style="white-space: nowrap;">
                            <div class="avatar avatar-sm mr-8pt">
                                <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                            </div>
                            <div class="media-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex d-flex flex-column">
                                        <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->addColumn('totalEvent', function ($item){
                return count($item->schedules);
            })
            ->addColumn('match', function ($item){
                $match = $item->schedules()->where('eventType', 'Match')->get();
                return count($match);
            })
            ->addColumn('training', function ($item){
                $match = $item->schedules()->where('eventType', 'Training')->get();
                return count($match);
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
        $query = $this->playerRepository->getCoachsPLayers($teams);
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
        $data = $this->eventScheduleRepository->playerEvent($player, '0', 'Training', sortDateDirection: 'desc');
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return '<a class="btn btn-sm btn-outline-secondary" href="' . route('training-schedules.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View training detail">
                            <span class="material-icons">visibility</span>
                        </a>';
            })
            ->editColumn('team', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[0]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[0]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[0]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('date', function ($item) {
                $date = date('M d, Y', strtotime($item->date));
                $startTime = date('h:i A', strtotime($item->startTime));
                $endTime = date('h:i A', strtotime($item->endTime));
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    return '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    return '<span class="badge badge-pill badge-danger">Ended</span>';
                }
            })
            ->editColumn('attendanceStatus', function ($item) {
                if ($item->pivot->attendanceStatus == 'Attended') {
                    return '<span class="badge badge-pill badge-success">Attended</span>';
                } else {
                    return '<span class="badge badge-pill badge-danger">'.$item->pivot->attendanceStatus.'</span>';
                }
            })
            ->editColumn('note', function ($item) {
                if ($item->pivot->note == null) {
                    return 'No note added';
                } else {
                    return $item->pivot->note;
                }
            })
            ->editColumn('last_updated', function ($item) {
                return date('M d, Y ~ h:i A', strtotime($item->pivot->updated_at));
            })
            ->rawColumns(['action','team','date','status', 'attendanceStatus', 'last_updated', 'note'])
            ->addIndexColumn()
            ->make();
    }
    public function dataTablesMatch(Player $player){
        $data = $this->eventScheduleRepository->playerEvent($player, '0', 'Match', sortDateDirection: 'desc');
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return '<a class="btn btn-sm btn-outline-secondary" href="' . route('match-schedules.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View match detail">
                            <span class="material-icons">visibility</span>
                        </a>';
            })
            ->editColumn('team', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[0]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[0]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[0]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('opponentTeam', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[1]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[1]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[1]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('competition', function ($item) {
                if ($item->competition){
                    $competition = '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->competition->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->competition->name . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->competition->type.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                }else{
                    $competition = 'No Competition';
                }
                return $competition;
            })
            ->editColumn('date', function ($item) {
                $date = date('M d, Y', strtotime($item->date));
                $startTime = date('h:i A', strtotime($item->startTime));
                $endTime = date('h:i A', strtotime($item->endTime));
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    return '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    return '<span class="badge badge-pill badge-danger">Ended</span>';
                }
            })
            ->editColumn('attendanceStatus', function ($item) {
                if ($item->pivot->attendanceStatus == 'Attended') {
                    return '<span class="badge badge-pill badge-success">Attended</span>';
                } else {
                    return '<span class="badge badge-pill badge-danger">'.$item->pivot->attendanceStatus.'</span>';
                }
            })
            ->editColumn('note', function ($item) {
                if ($item->pivot->note == null) {
                    return 'No note added';
                } else {
                    return $item->pivot->note;
                }
            })
            ->editColumn('last_updated', function ($item) {
                return date('M d, Y ~ h:i A', strtotime($item->pivot->updated_at));
            })
            ->rawColumns(['action','team', 'competition','opponentTeam','date','status', 'attendanceStatus', 'last_updated', 'note'])
            ->addIndexColumn()
            ->make();
    }
}
