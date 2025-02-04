<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Player;
use App\Repository\Interface\PlayerRepositoryInterface;
use App\Repository\Interface\TeamRepositoryInterface;
use App\Repository\Interface\TrainingRepositoryInterface;
use App\Repository\MatchRepository;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use TheSeer\Tokenizer\NamespaceUri;
use Yajra\DataTables\Facades\DataTables;

class AttendanceReportService extends Service
{
    private PlayerRepositoryInterface $playerRepository;
    private MatchRepository $matchRepository;
    private TrainingRepositoryInterface $trainingRepository;
    private TeamRepositoryInterface $teamRepository;
    private MatchService $matchService;
    private TrainingService $trainingService;
    private DatatablesHelper $datatablesHelper;
    public function __construct(
        PlayerRepositoryInterface $playerRepository,
        MatchRepository  $matchRepository,
        TrainingRepositoryInterface $trainingRepository,
        TeamRepositoryInterface   $teamRepository,
        MatchService $matchService,
        TrainingService $trainingService,
        DatatablesHelper $datatablesHelper
    )
    {
        $this->playerRepository = $playerRepository;
        $this->matchRepository = $matchRepository;
        $this->trainingRepository = $trainingRepository;
        $this->datatablesHelper = $datatablesHelper;
        $this->teamRepository = $teamRepository;
        $this->matchService = $matchService;
        $this->trainingService = $trainingService;
    }

    public function playerMatchIllness(Player $player, $startDate = null, $endDate = null): int
    {
        return  $this->matchRepository->playerAttendance($player, 'Illness', $startDate, $endDate);
    }
    public function playerMatchOthers(Player $player, $startDate = null, $endDate = null): int
    {
        return  $this->matchRepository->playerAttendance($player, 'Others', $startDate, $endDate);
    }
    public function playerMatchRequiredAction(Player $player, $startDate = null, $endDate = null): int
    {
        return  $this->matchRepository->playerAttendance($player, 'Required Action', $startDate, $endDate);
    }
    public function playerMatchInjured(Player $player, $startDate = null, $endDate = null): int
    {
        return  $this->matchRepository->playerAttendance($player, 'Injured', $startDate, $endDate);
    }
    public function playerMatchAttended(Player $player, $startDate = null, $endDate = null): int
    {
        return  $this->matchRepository->playerAttendance($player, 'Attended', $startDate, $endDate);
    }
    public function playerMatchTotalAbsent(Player $player, $startDate = null, $endDate = null): string
    {
        $illness = $this->playerMatchIllness($player, $startDate, $endDate);
        $injured = $this->playerMatchInjured($player, $startDate, $endDate);
        $others = $this->playerMatchOthers($player, $startDate, $endDate);

        $totalDidntAttended = $illness + $injured + $others;
        $totalEvent = $this->matchService->playerTotalMatch($player, $startDate, $endDate);
        if ($totalEvent < 1){
            return 'No event yet';
        }else{
            $percentage = $totalDidntAttended/$totalEvent*100;
            return $totalDidntAttended . ' ('.round($percentage, 1).'%)';
        }
    }


    public function playerTrainingIllness(Player $player, $startDate = null, $endDate = null): int
    {
        return  $this->trainingRepository->playerAttendance($player, 'Illness', $startDate, $endDate);
    }
    public function playerTrainingOthers(Player $player, $startDate = null, $endDate = null): int
    {
        return  $this->trainingRepository->playerAttendance($player, 'Others', $startDate, $endDate);
    }
    public function playerTrainingRequiredAction(Player $player, $startDate = null, $endDate = null): int
    {
        return  $this->trainingRepository->playerAttendance($player, 'Required Action', $startDate, $endDate);
    }
    public function playerTrainingInjured(Player $player, $startDate = null, $endDate = null): int
    {
        return  $this->trainingRepository->playerAttendance($player, 'Injured', $startDate, $endDate);
    }
    public function playerTrainingAttended(Player $player, $startDate = null, $endDate = null): int
    {
        return  $this->trainingRepository->playerAttendance($player, 'Attended', $startDate, $endDate);
    }
    public function playerTrainingTotalAbsent(Player $player, $startDate = null, $endDate = null): string
    {
        $illness = $this->playerTrainingIllness($player, $startDate, $endDate);
        $injured = $this->playerTrainingInjured($player, $startDate, $endDate);
        $others = $this->playerTrainingOthers($player, $startDate, $endDate);

        $totalDidntAttended = $illness + $injured + $others;
        $totalEvent = $this->trainingService->playerTotalTraining($player, $startDate, $endDate, ['Completed']);
        if ($totalEvent < 1){
            return 'No event yet';
        }else{
            $percentage = $totalDidntAttended / $totalEvent * 100;
            return $totalDidntAttended . ' ('.round($percentage, 1).'%)';
        }
    }



    public function matchPlayersAttendanceDatatables($teams = null, $startDate = null, $endDate = null): JsonResponse
    {
        $query = $this->playerRepository->getAll($teams);
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('attendance-report.show', $item->hash), 'View player attendance detail', 'visibility');
            })
            ->editColumn('teams', function ($item) {
                return $this->datatablesHelper->usersTeams($item);
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->addColumn('totalMatch', function ($item) use ($startDate, $endDate){
                return $this->matchService->playerTotalMatch($item);
            })
            ->addColumn('attended', function ($item) use ($startDate, $endDate) {
                return $this->playerMatchAttended($item, $startDate, $endDate);
            })
            ->addColumn('absent', function ($item) use ($startDate, $endDate) {
                return $this->playerMatchTotalAbsent($item, $startDate, $endDate);
            })
            ->addColumn('illness', function ($item) use ($startDate, $endDate) {
                return $this->playerMatchIllness($item, $startDate, $endDate);
            })
            ->addColumn('injured', function ($item) use ($startDate, $endDate) {
                return $this->playerMatchInjured($item, $startDate, $endDate);
            })
            ->addColumn('others', function ($item) use ($startDate, $endDate) {
                return $this->playerMatchOthers($item, $startDate, $endDate);
            })
            ->addColumn('requiredAction', function ($item) use ($startDate, $endDate) {
                return $this->playerMatchRequiredAction($item, $startDate, $endDate);
            })
            ->rawColumns(['action','teams', 'name'])
            ->addIndexColumn()
            ->make();
    }
    public function trainingPlayersAttendanceDatatables($teams = null, $startDate = null, $endDate = null): JsonResponse
    {
        $query = $this->playerRepository->getAll($teams);
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('attendance-report.show', $item->hash), 'View player attendance detail', 'visibility');
            })
            ->editColumn('teams', function ($item) {
                return $this->datatablesHelper->usersTeams($item);
            })
            ->editColumn('name', function ($item) {
                return $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash));
            })
            ->addColumn('totalTraining', function ($item) use ($startDate, $endDate){
                return $this->trainingService->playerTotalTraining($item, $startDate, $endDate, ['Completed']);
            })
            ->addColumn('attended', function ($item) use ($startDate, $endDate) {
                return $this->playerTrainingAttended($item, $startDate, $endDate);
            })
            ->addColumn('absent', function ($item) use ($startDate, $endDate) {
                return $this->playerTrainingTotalAbsent($item, $startDate, $endDate);
            })
            ->addColumn('illness', function ($item) use ($startDate, $endDate) {
                return $this->playerTrainingIllness($item, $startDate, $endDate);
            })
            ->addColumn('injured', function ($item) use ($startDate, $endDate) {
                return $this->playerTrainingInjured($item, $startDate, $endDate);
            })
            ->addColumn('others', function ($item) use ($startDate, $endDate) {
                return $this->playerTrainingOthers($item, $startDate, $endDate);
            })
            ->addColumn('requiredAction', function ($item) use ($startDate, $endDate) {
                return $this->playerTrainingRequiredAction($item, $startDate, $endDate);
            })
            ->rawColumns(['action','teams', 'name'])
            ->addIndexColumn()
            ->make();
    }



    public function matchIndex($startDate = null, $endDate = null, $teams = null): JsonResponse
    {
        if ($teams != null) {
            $teams = $this->teamRepository->find($teams);
        }

        $data = $this->matchRepository->getAll(teams: $teams, status: ['Completed'], startDate: $startDate, endDate:  $endDate);

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('match-schedules.show', $item->hash), 'View match session', 'visibility');
            })
            ->editColumn('eventName', function ($item) {
                return match (true) {
                    $item->matchType === 'Internal Match' => $item->homeTeam->teamName . ' Vs. ' . $item->awayTeam->teamName,
                    default => $item->homeTeam->teamName . ' Vs. ' . $item->externalTeam->teamName,
                };
            })
            ->addColumn('totalPlayers', function ($item) {
                return $item->players()->count();
            })
            ->addColumn('playerAttended', function ($item) {
                return $this->matchRepository->getRelationData($item, 'players', attendanceStatus: 'Attended', retrieveType: 'count');
            })
            ->addColumn('playerIllness', function ($item) {
                return $this->matchRepository->getRelationData($item, 'players', attendanceStatus: 'Illness', retrieveType: 'count');
            })
            ->addColumn('playerInjured', function ($item) {
                return $this->matchRepository->getRelationData($item, 'players', attendanceStatus: 'Injured', retrieveType: 'count');
            })
            ->addColumn('playerOther', function ($item) {
                return $this->matchRepository->getRelationData($item, 'players', attendanceStatus: 'Other', retrieveType: 'count');
            })
            ->addColumn('playerRequiredAction', function ($item) {
                return $this->matchRepository->getRelationData($item, 'players', attendanceStatus: 'Required Action', retrieveType: 'count');
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->eventStatus($item->status);
            })
            ->rawColumns(['action', 'team', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function trainingIndex($startDate = null, $endDate = null, $teams = null): JsonResponse
    {
        if ($teams != null) {
            $teams = $this->teamRepository->find($teams);
        }

        $data = $this->trainingRepository->getAll(team: $teams, status: ['Completed'], startDate: $startDate, endDate:  $endDate);

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('training-schedules.show', $item->hash), 'View training session', 'visibility');
            })
            ->editColumn('team', function ($item) {
                return $this->datatablesHelper->name($item->team->logo, $item->team->teamName, $item->team->ageGroup, route('team-managements.show', $item->hash));
            })
            ->editColumn('eventName', function ($item) {
                return $item->topic;
            })
            ->addColumn('totalPlayers', function ($item) {
                return $item->players()->count();
            })
            ->addColumn('playerAttended', function ($item) {
                return $this->trainingService->playerAttended($item);
            })
            ->addColumn('playerIllness', function ($item) {
                return $this->trainingService->playerIllness($item);
            })
            ->addColumn('playerInjured', function ($item) {
                return $this->trainingService->playerInjured($item);
            })
            ->addColumn('playerOther', function ($item) {
                return $this->trainingService->playerOther($item);
            })
            ->addColumn('playerRequiredAction', function ($item) {
                return $this->trainingRepository->getRelationData($item, 'players', attendanceStatus: 'Required Action', retrieveType: 'count');
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->eventStatus($item->status);
            })
            ->rawColumns(['action', 'team', 'status'])
            ->addIndexColumn()
            ->make();
    }



    public function mostAttendedPlayer($startDate = null, $endDate = null, $teams = null, $eventType = 'training'): array
    {
        $results = $this->playerRepository->getMostAttended(startDate: $startDate, endDate:  $endDate, teams: $teams, relation:  $eventType);
        if ($results != null and $results->schedules_count > 0) {
            $attended_count = $results->attended_count;
            $schedules_count = $results->schedules_count;
            $mostAttendedPercentage = round($attended_count / $schedules_count * 100, 1);
        } else {
            $mostAttendedPercentage = null;
        }

        return compact('results', 'mostAttendedPercentage');
    }
    public function mostDidntAttendPlayer($startDate = null, $endDate = null, $teams = null, $eventType = 'training'): array
    {
        $results = $this->playerRepository->getMostDidntAttended(startDate: $startDate, endDate:  $endDate, teams: $teams, relation:  $eventType);
        if ($results != null and $results->schedules_count > 0) {
            $didnt_attended_count = $results->didnt_attended_count;
            $schedules_count = $results->schedules_count;
            $mostDidntAttendPercentage = round($didnt_attended_count / $schedules_count * 100, 1);
        } else {
            $mostDidntAttendPercentage = null;
        }
        return compact('results', 'mostDidntAttendPercentage');
    }



    public function matchAttendanceHistoryChart($startDate = null, $endDate = null,  $teams = null)
    {
        $attendanceData = $this->matchRepository->getAttendanceTrend($startDate, $endDate, $teams);
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
    public function matchAttendanceStatusChart($startDate = null, $endDate = null, $teams = null): array
    {
        $result = $this->matchRepository->countAttendanceByStatus($startDate, $endDate, $teams);

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


    public function trainingAttendanceHistoryChart($startDate = null, $endDate = null,  $teams = null)
    {
        $attendanceData = $this->trainingRepository->getAttendanceTrend($startDate, $endDate, $teams);
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
    public function trainingAttendanceStatusChart($startDate = null, $endDate = null, $teams = null): array
    {
        $result = $this->trainingRepository->countAttendanceByStatus($startDate, $endDate, $teams);
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



    public function playerTrainings(Player $player, $startDate = null, $endDate = null): JsonResponse
    {
        $data = $this->trainingRepository->getByRelation($player, ['team'], ['Completed'], $startDate, $endDate);
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('training-schedules.show', $item->hash), 'View training session', 'visibility');
            })
            ->editColumn('team', function ($item) {
                return $this->datatablesHelper->name($item->team->logo, $item->team->teamName, $item->team->ageGroup);
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesHelper->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->eventStatus($item->status);
            })
            ->editColumn('attendanceStatus', function ($item) {
                return $this->datatablesHelper->attendanceStatus($item->pivot->attendanceStatus);
            })
            ->editColumn('note', function ($item) {
                return ($item->pivot->note == null) ? 'No note added' : $item->pivot->note;
            })
            ->editColumn('last_updated', function ($item) {
                return $this->convertToDatetime($item->pivot->updated_at);
            })
            ->rawColumns(['action','team','date','status', 'attendanceStatus', 'note'])
            ->addIndexColumn()
            ->make();
    }
    public function playerMatch(Player $player, $startDate = null, $endDate = null): JsonResponse
    {
        $data = $this->matchRepository->getByRelation($player, ['homeTeam', 'awayTeam', 'externalTeam','competition'], status: ['Completed'], startDate:  $startDate, endDate: $endDate);
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('match-schedules.show', $item->hash), 'View match session', 'visibility');
            })
            ->editColumn('team', function ($item) {
                return ($item->homeTeam) ? $this->datatablesHelper->name($item->homeTeam->logo, $item->homeTeam->teamName, $item->homeTeam->ageGroup, route('team-managements.show', $item->homeTeam->hash)) : 'No team';
            })
            ->editColumn('opponentTeam', function ($item) {
                ($item->matchType == 'Internal Match')
                    ? $team = ($item->awayTeam)
                        ? $this->datatablesHelper->name($item->awayTeam->logo, $item->awayTeam->teamName, $item->awayTeam->ageGroup, route('team-managements.show', $item->awayTeam->hash))
                        : 'No team'
                    : $team = ($item->externalTeam)
                        ? $item->externalTeam->teamName
                        : 'No team';
                return $team;
            })
            ->editColumn('competition', function ($item) {
                ($item->competition) ? $competition = $this->datatablesHelper->name($item->competition->logo, $item->competition->name, $item->competition->type) : $competition = 'No Competition';
                return $competition;
            })
            ->editColumn('date', function ($item) {
                return $this->datatablesHelper->startEndDate($item);
            })
            ->editColumn('status', function ($item) {
                return $this->datatablesHelper->eventStatus($item->status);
            })
            ->editColumn('attendanceStatus', function ($item) {
                return $this->datatablesHelper->attendanceStatus($item->pivot->attendanceStatus);
            })
            ->editColumn('note', function ($item) {
                ($item->pivot->note == null) ? $data = 'No note added' : $data = $item->pivot->note;
                return $data;
            })
            ->editColumn('last_updated', function ($item) {
                return $this->convertToDatetime($item->pivot->updated_at);
            })
            ->rawColumns(['action','team', 'competition','opponentTeam','status', 'attendanceStatus'])
            ->addIndexColumn()
            ->make();
    }
}
