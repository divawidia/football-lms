<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use App\Repository\TeamRepository;
use App\Services\AttendanceReportService;
use App\Services\TeamService;
use Carbon\Carbon;
use Exception;
use Hamcrest\Core\IsNot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    private AttendanceReportService $attendanceReportService;
    private TeamService $teamService;
    private TeamRepository $teamRepository;

    public function __construct(
        AttendanceReportService $attendanceReportService,
        TeamService $teamService,
        TeamRepository $teamRepository
    )
    {
        $this->attendanceReportService = $attendanceReportService;
        $this->teamService = $teamService;
        $this->teamRepository = $teamRepository;
    }

    public function adminCoachIndex()
    {
        ($this->isAllAdmin()) ? $teams = $this->teamService->allTeams() : $teams = $this->getLoggedCoachUser()->teams;
        return view('pages.academies.reports.attendances.admin-coach-index', [
            'teams' => $teams,
            'playerAttendanceDatatablesRoute' => route('attendance-report.admin-coach-index'),
        ]);
    }
    public function playerIndex()
    {
        return view('pages.academies.reports.attendances.player-detail', [
            'player' => $this->getLoggedPLayerUser(),
        ]);
    }

    public function attendanceData(Request $request): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');

        if ($team == null and isCoach()) {
            $team = $this->getLoggedCoachUser()->teams;
        }
        elseif ($team != null) {
            $team = $this->teamRepository->find($team);
        }

        $data = [
            'matchAttendanceHistoryChart' => $this->attendanceReportService->matchAttendanceHistoryChart($startDate, $endDate, $team),
            'matchAttendanceStatusChart' => $this->attendanceReportService->matchAttendanceStatusChart($startDate, $endDate, $team),
            'trainingAttendanceHistoryChart' => $this->attendanceReportService->trainingAttendanceHistoryChart($startDate, $endDate, $team),
            'trainingAttendanceStatusChart' => $this->attendanceReportService->trainingAttendanceStatusChart($startDate, $endDate, $team),
            'matchMostAttendedPlayer' => $this->attendanceReportService->mostAttendedPlayer($startDate, $endDate, $team, 'matches'),
            'matchMostDidntAttendPlayer' => $this->attendanceReportService->mostDidntAttendPlayer($startDate, $endDate, $team, 'matches'),
            'trainingMostAttendedPlayer' => $this->attendanceReportService->mostAttendedPlayer($startDate, $endDate, $team, 'trainings'),
            'trainingMostDidntAttendPlayer' => $this->attendanceReportService->mostDidntAttendPlayer($startDate, $endDate, $team, 'trainings'),
        ];

        return ApiResponse::success($data);
    }


    public function matchPlayersAttendanceIndex(Request $request): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');

        if ($team == null and isCoach()) {
            $team = $this->getLoggedCoachUser()->teams;
        }
        elseif ($team != null) {
            $team = $this->teamRepository->find($team);
        }

        return $this->attendanceReportService->matchPlayersAttendanceDatatables($startDate, $endDate, $team);
    }

    public function trainingPlayersAttendanceIndex(Request $request): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');

        if ($team == null and isCoach()) {
            $team = $this->getLoggedCoachUser()->teams;
        }
        elseif ($team != null) {
            $team = $this->teamRepository->find($team);
        }

        return $this->attendanceReportService->trainingPlayersAttendanceDatatables($startDate, $endDate, $team);
    }


    public function matchesAttendanceIndex(Request $request): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');

        if ($team == null and isCoach()) {
            $team = $this->getLoggedCoachUser()->teams;
        }
        elseif ($team != null) {
            $team = $this->teamRepository->find($team);
        }

        return $this->attendanceReportService->matchIndex($startDate, $endDate, $team);
    }
    public function trainingAttendanceIndex(Request $request): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');

        if ($team == null and isCoach()) {
            $team = $this->getLoggedCoachUser()->teams;
        }
        elseif ($team != null) {
            $team = $this->teamRepository->find($team);
        }

        return $this->attendanceReportService->trainingIndex($startDate, $endDate, $team);
    }


    public function show(Player $player){
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();
        return view('pages.academies.reports.attendances.player-detail', [
            'player' => $player,
            'playerMatchIllness' => $this->attendanceReportService->playerMatchIllness($player),
            'playerMatchOthers' => $this->attendanceReportService->playerMatchOthers($player),
            'playerMatchRequiredAction' => $this->attendanceReportService->playerMatchRequiredAction($player),
            'playerMatchInjured' => $this->attendanceReportService->playerMatchInjured($player),
            'playerMatchAttended' => $this->attendanceReportService->playerMatchAttended($player),
            'playerMatchTotalAbsent' => $this->attendanceReportService->playerMatchTotalAbsent($player),
            'playerTrainingIllness' => $this->attendanceReportService->playerTrainingIllness($player),
            'playerTrainingOthers' => $this->attendanceReportService->playerTrainingOthers($player),
            'playerTrainingRequiredAction' => $this->attendanceReportService->playerTrainingRequiredAction($player),
            'playerTrainingInjured' => $this->attendanceReportService->playerTrainingInjured($player),
            'playerTrainingAttended' => $this->attendanceReportService->playerTrainingAttended($player),
            'playerTrainingTotalAbsent' => $this->attendanceReportService->playerTrainingTotalAbsent($player),
            'playerMatchIllnessThisMonth' => $this->attendanceReportService->playerMatchIllness($player, $startDate, $endDate),
            'playerMatchOthersThisMonth' => $this->attendanceReportService->playerMatchOthers($player, $startDate, $endDate),
            'playerMatchRequiredActionThisMonth' => $this->attendanceReportService->playerMatchRequiredAction($player, $startDate, $endDate),
            'playerMatchInjuredThisMonth' => $this->attendanceReportService->playerMatchInjured($player, $startDate, $endDate),
            'playerMatchAttendedThisMonth' => $this->attendanceReportService->playerMatchAttended($player, $startDate, $endDate),
            'playerMatchTotalAbsentThisMonth' => $this->attendanceReportService->playerMatchTotalAbsent($player, $startDate, $endDate),
            'playerTrainingIllnessThisMonth' => $this->attendanceReportService->playerTrainingIllness($player, $startDate, $endDate),
            'playerTrainingOthersThisMonth' => $this->attendanceReportService->playerTrainingOthers($player, $startDate, $endDate),
            'playerTrainingRequiredActionThisMonth' => $this->attendanceReportService->playerTrainingRequiredAction($player, $startDate, $endDate),
            'playerTrainingInjuredThisMonth' => $this->attendanceReportService->playerTrainingInjured($player, $startDate, $endDate),
            'playerTrainingAttendedThisMonth' => $this->attendanceReportService->playerTrainingAttended($player, $startDate, $endDate),
            'playerTrainingTotalAbsentThisMonth' => $this->attendanceReportService->playerTrainingTotalAbsent($player, $startDate, $endDate),
        ]);
    }

    public function playerMatchIndex(Request $request, Player $player): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        return $this->attendanceReportService->playerMatch($player, $startDate, $endDate);
    }

    public function playerTrainingIndex(Request $request, Player $player): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        return $this->attendanceReportService->playerTrainings($player, $startDate, $endDate);
    }

    public function playerAttendanceData(Request $request, Player $player): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $data = [
            'playerMatchIllness' => $this->attendanceReportService->playerMatchIllness($player, $startDate, $endDate),
            'playerMatchOthers' => $this->attendanceReportService->playerMatchOthers($player, $startDate, $endDate),
            'playerMatchRequiredAction' => $this->attendanceReportService->playerMatchRequiredAction($player, $startDate, $endDate),
            'playerMatchInjured' => $this->attendanceReportService->playerMatchInjured($player, $startDate, $endDate),
            'playerMatchAttended' => $this->attendanceReportService->playerMatchAttended($player, $startDate, $endDate),
            'playerMatchTotalAbsent' => $this->attendanceReportService->playerMatchTotalAbsent($player, $startDate, $endDate),
            'playerTrainingIllness' => $this->attendanceReportService->playerTrainingIllness($player, $startDate, $endDate),
            'playerTrainingOthers' => $this->attendanceReportService->playerTrainingOthers($player, $startDate, $endDate),
            'playerTrainingRequiredAction' => $this->attendanceReportService->playerTrainingRequiredAction($player, $startDate, $endDate),
            'playerTrainingInjured' => $this->attendanceReportService->playerTrainingInjured($player, $startDate, $endDate),
            'playerTrainingAttended' => $this->attendanceReportService->playerTrainingAttended($player, $startDate, $endDate),
            'playerTrainingTotalAbsent' => $this->attendanceReportService->playerTrainingTotalAbsent($player, $startDate, $endDate),
        ];

        return ApiResponse::success($data);
    }

    public function playerMatchHistories()
    {
        return $this->attendanceReportService->playerMatch($this->getLoggedPLayerUser());
    }
    public function playerTrainingHistories()
    {
        return $this->attendanceReportService->playerTrainings($this->getLoggedPLayerUser());
    }
}
