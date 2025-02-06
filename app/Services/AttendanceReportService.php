<?php

namespace App\Services;

use App\Helpers\DatatablesHelper;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Team;
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

    private function getTeam(?string $teamId, ?Coach $loggedCoach)
    {
        return $teamId ? $this->teamRepository->whereId($teamId) : ($loggedCoach ? $loggedCoach->teams : null);
    }

    public function getPlayerAttendance(Player $player, string $status, $startDate = null, $endDate = null, bool $isMatch = true): int
    {
        return $isMatch
            ? $this->matchRepository->playerAttendance($player, $status, $startDate, $endDate)
            : $this->trainingRepository->playerAttendance($player, $status, $startDate, $endDate);
    }

    public function getTotalAbsent(Player $player, $startDate = null, $endDate = null, bool $isMatch = true): string
    {
        $illness = $this->getPlayerAttendance($player, 'Illness', $startDate, $endDate, $isMatch);
        $injured = $this->getPlayerAttendance($player, 'Injured', $startDate, $endDate, $isMatch);
        $others = $this->getPlayerAttendance($player, 'Others', $startDate, $endDate, $isMatch);
        $requiredAction = $this->getPlayerAttendance($player, 'Required Action', $startDate, $endDate, $isMatch);

        $totalDidntAttended = $illness + $injured + $others + $requiredAction;
        $totalEvent = $isMatch
            ? $this->matchRepository->getAll(player: $player, status: ['Completed'], startDate: $startDate, endDate: $endDate)->count()
            : $this->trainingService->playerTotalTraining($player, $startDate, $endDate, ['Completed']);

        return $totalEvent < 1
            ? 'No event yet'
            : "$totalDidntAttended (" . round(($totalDidntAttended / $totalEvent) * 100, 1) . '%)';
    }

    private function generatePlayerAttendanceDatatables($query, $startDate, $endDate, $isMatch = true)
    {
        return Datatables::of($query)
            ->addColumn('action', fn($item) => $this->datatablesHelper->buttonTooltips(route('attendance-report.show', $item->hash), 'View player attendance detail', 'visibility'))
            ->editColumn('teams', fn($item) => $this->datatablesHelper->usersTeams($item))
            ->editColumn('name', fn($item) => $this->datatablesHelper->name($item->user->foto, $this->getUserFullName($item->user), $item->position->name, route('player-managements.show', $item->hash)))
            ->addColumn($isMatch ? 'totalMatch' : 'totalTraining', fn($item) => $isMatch ? $this->matchRepository->getAll(player: $item, status: ['Completed'], startDate: $startDate, endDate: $endDate)->count() : $this->trainingService->playerTotalTraining($item, $startDate, $endDate, ['Completed']))
            ->addColumn('attended', fn($item) => $this->getPlayerAttendance($item, 'Attended', $startDate, $endDate, $isMatch))
            ->addColumn('absent', fn($item) => $this->getTotalAbsent($item, $startDate, $endDate, $isMatch))
            ->addColumn('illness', fn($item) => $this->getPlayerAttendance($item, 'Illness', $startDate, $endDate, $isMatch))
            ->addColumn('injured', fn($item) => $this->getPlayerAttendance($item, 'Injured', $startDate, $endDate, $isMatch))
            ->addColumn('others', fn($item) => $this->getPlayerAttendance($item, 'Others', $startDate, $endDate, $isMatch))
            ->addColumn('requiredAction', fn($item) => $this->getPlayerAttendance($item, 'Required Action', $startDate, $endDate, $isMatch))
            ->rawColumns(['action', 'teams', 'name'])
            ->addIndexColumn()
            ->make();
    }



    public function matchPlayersAttendanceDatatables(string $teamId = null, $startDate = null, $endDate = null, Coach $loggedCoach = null): JsonResponse
    {
        $team = $this->getTeam($teamId, $loggedCoach);
        $query = $this->playerRepository->getAll($team);
        return $this->generatePlayerAttendanceDatatables($query, $startDate, $endDate, true);
    }

    public function trainingPlayersAttendanceDatatables(string $teamId = null, $startDate = null, $endDate = null, Coach $loggedCoach = null): JsonResponse
    {
        $team = $this->getTeam($teamId, $loggedCoach);
        $query = $this->playerRepository->getAll($team);
        return $this->generatePlayerAttendanceDatatables($query, $startDate, $endDate, false);
    }



    private function generateEventDatatables($data, $isMatch = true)
    {
        return Datatables::of($data)
            ->addColumn('action', fn($item) => $this->datatablesHelper->buttonTooltips(route($isMatch ? 'match-schedules.show' : 'training-schedules.show', $item->hash), $isMatch ? 'View match session' : 'View training session', 'visibility'))
            ->editColumn('eventName', fn($item) => $isMatch ? ($item->matchType === 'Internal Match' ? $item->homeTeam->teamName . ' Vs. ' . $item->awayTeam->teamName : $item->homeTeam->teamName . ' Vs. ' . $item->externalTeam->teamName) : $item->topic)
            ->addColumn('totalPlayers', fn($item) => $item->players()->count())
            ->addColumn('playerAttended', fn($item) => $isMatch ? $this->matchRepository->getRelationData($item, 'players', attendanceStatus: 'Attended', retrieveType: 'count') : $this->trainingService->playerAttended($item))
            ->addColumn('playerIllness', fn($item) => $isMatch ? $this->matchRepository->getRelationData($item, 'players', attendanceStatus: 'Illness', retrieveType: 'count') : $this->trainingService->playerIllness($item))
            ->addColumn('playerInjured', fn($item) => $isMatch ? $this->matchRepository->getRelationData($item, 'players', attendanceStatus: 'Injured', retrieveType: 'count') : $this->trainingService->playerInjured($item))
            ->addColumn('playerOther', fn($item) => $isMatch ? $this->matchRepository->getRelationData($item, 'players', attendanceStatus: 'Other', retrieveType: 'count') : $this->trainingService->playerOther($item))
            ->addColumn('playerRequiredAction', fn($item) => $isMatch ? $this->matchRepository->getRelationData($item, 'players', attendanceStatus: 'Required Action', retrieveType: 'count') : $this->trainingService->playerRequiredAction($item))
            ->editColumn('status', fn($item) => $this->datatablesHelper->eventStatus($item->status))
            ->rawColumns(['action', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function matchIndex($startDate = null, $endDate = null, string $teamId = null, Coach $loggedCoach = null): JsonResponse
    {
        $team = $this->getTeam($teamId, $loggedCoach);
        $data = $this->matchRepository->getAll(teams: $team, status: ['Completed'], startDate: $startDate, endDate: $endDate);
        return $this->generateEventDatatables($data, true);
    }

    public function trainingIndex($startDate = null, $endDate = null, string $teamId = null, Coach $loggedCoach = null): JsonResponse
    {
        $team = $this->getTeam($teamId, $loggedCoach);
        $data = $this->trainingRepository->getAll(teams: $team, status: ['Completed'], startDate: $startDate, endDate: $endDate);
        return $this->generateEventDatatables($data, false);
    }



    private function calculateAttendancePercentage($results, $attendedKey): ?float
    {
        return ($results && $results->schedules_count > 0)
            ? round(($results->$attendedKey / $results->schedules_count) * 100, 1)
            : null;
    }

    public function mostAttendedPlayer($startDate = null, $endDate = null, $eventType = 'training', string $teamId = null, Coach $loggedCoach = null): array
    {
        $team = $this->getTeam($teamId, $loggedCoach);
        $results = $this->playerRepository->getMostAttended($startDate, $endDate, $team, $eventType);
        return [
            'results' => $results,
            'mostAttendedPercentage' => $this->calculateAttendancePercentage($results, 'attended_count')
        ];
    }

    public function mostDidntAttendPlayer($startDate = null, $endDate = null, $eventType = 'training', string $teamId = null, Coach $loggedCoach = null): array
    {
        $team = $this->getTeam($teamId, $loggedCoach);
        $results = $this->playerRepository->getMostDidntAttended($startDate, $endDate, $team, $eventType);
        return [
            'results' => $results,
            'mostDidntAttendPercentage' => $this->calculateAttendancePercentage($results, 'didnt_attended_count')
        ];
    }


    private function generateAttendanceHistoryChart($data): array
    {
        return [
            'labels' => $data->pluck('date'),
            'datasets' => [
                [
                    'label' => 'Total number of attended players',
                    'data' => $data->pluck('total_attended_players'),
                    'borderColor' => '#20F4CB',
                    'tension' => 0.4,
                ],[
                    'label' => 'Total number of ill players',
                    'data' => $data->pluck('total_of_ill_players'),
                    'borderColor' => '#E52534',
                    'tension' => 0.4,
                ],[
                    'label' => 'Total number of injured players',
                    'data' => $data->pluck('total_of_injured_players'),
                    'borderColor' => '#F9B300',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Total number of other status players',
                    'data' => $data->pluck('total_of_other_attendance_status_players'),
                    'borderColor' => '#00122A',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Total number of required action status players',
                    'data' => $data->pluck('total_of_required_action_status_players'),
                    'borderColor' => '#690d0d',
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    private function generateAttendanceStatusChart($data): array
    {
        return [
            'labels' => $data->pluck('status'),
            'datasets' => [
                [
                    'label' => 'Total number of players',
                    'data' => $data->pluck('total_players'),
                    'backgroundColor' => ['#20F4CB', '#E52534', '#F9B300', '#00122A', '#690d0d'],
                    'tension' => 0.4,
                ]
            ],
        ];
    }

    public function matchAttendanceHistoryChart($startDate = null, $endDate = null, string $teamId = null, Coach $loggedCoach = null): array
    {
        $team = $this->getTeam($teamId, $loggedCoach);
        $attendanceData = $this->matchRepository->getAttendanceTrend($startDate, $endDate, $team);
        return $this->generateAttendanceHistoryChart($attendanceData);
    }

    public function matchAttendanceStatusChart($startDate = null, $endDate = null, string $teamId = null, Coach $loggedCoach = null): array
    {
        $team = $this->getTeam($teamId, $loggedCoach);
        $result = $this->matchRepository->countAttendanceByStatus($startDate, $endDate, $team);
        return $this->generateAttendanceStatusChart($result);
    }

    public function trainingAttendanceHistoryChart($startDate = null, $endDate = null, string $teamId = null, Coach $loggedCoach = null): array
    {
        $team = $this->getTeam($teamId, $loggedCoach);
        $attendanceData = $this->trainingRepository->getAttendanceTrend($startDate, $endDate, $team);
        return $this->generateAttendanceHistoryChart($attendanceData);
    }

    public function trainingAttendanceStatusChart($startDate = null, $endDate = null, string $teamId = null, Coach $loggedCoach = null): array
    {
        $team = $this->getTeam($teamId, $loggedCoach);
        $result = $this->trainingRepository->countAttendanceByStatus($startDate, $endDate, $team);
        return $this->generateAttendanceStatusChart($result);
    }



    public function playerTrainings(Player $player, $startDate = null, $endDate = null): JsonResponse
    {
        $data = $this->trainingRepository->getByRelation($player, ['team'], ['Completed'], $startDate, $endDate, orderDirection: 'desc');
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
        $data = $this->matchRepository->getByRelation($player, ['homeTeam', 'awayTeam', 'externalTeam','competition'], status: ['Completed'], startDate:  $startDate, endDate: $endDate, orderDirection: 'desc');
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                return $this->datatablesHelper->buttonTooltips(route('match-schedules.show', $item->hash), 'View match session', 'visibility');
            })
            ->editColumn('team', function ($item) {
                return ($item->homeTeam) ? $this->datatablesHelper->name($item->homeTeam->logo, $item->homeTeam->teamName, $item->homeTeam->ageGroup, route('team-managements.show', $item->homeTeam->hash)) : 'No team';
            })
            ->editColumn('score', function ($item) {
                return $this->matchService->matchScores($item);
            })
            ->editColumn('opponentTeam', function ($item) {
                return $this->matchService->awayTeamDatatables($item);
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
            ->rawColumns(['action','team', 'score','competition','opponentTeam','status', 'attendanceStatus'])
            ->addIndexColumn()
            ->make();
    }
}
