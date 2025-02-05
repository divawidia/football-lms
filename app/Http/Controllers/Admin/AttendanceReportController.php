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

    public function __construct(
        AttendanceReportService $attendanceReportService,
        TeamService $teamService,
    )
    {
        $this->attendanceReportService = $attendanceReportService;
        $this->teamService = $teamService;
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
        $coach = $this->getLoggedCoachUser();
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');

        $data = [
            'matchAttendanceHistoryChart' => $this->attendanceReportService->matchAttendanceHistoryChart($startDate, $endDate, $team, $coach),
            'matchAttendanceStatusChart' => $this->attendanceReportService->matchAttendanceStatusChart($startDate, $endDate, $team, $coach),
            'trainingAttendanceHistoryChart' => $this->attendanceReportService->trainingAttendanceHistoryChart($startDate, $endDate, $team, $coach),
            'trainingAttendanceStatusChart' => $this->attendanceReportService->trainingAttendanceStatusChart($startDate, $endDate, $team, $coach),
            'matchMostAttendedPlayer' => $this->attendanceReportService->mostAttendedPlayer($startDate, $endDate, 'matches', $team, $coach),
            'matchMostDidntAttendPlayer' => $this->attendanceReportService->mostDidntAttendPlayer($startDate, $endDate, 'matches',$team, $coach),
            'trainingMostAttendedPlayer' => $this->attendanceReportService->mostAttendedPlayer($startDate, $endDate, 'trainings', $team, $coach),
            'trainingMostDidntAttendPlayer' => $this->attendanceReportService->mostDidntAttendPlayer($startDate, $endDate, 'trainings', $team, $coach),
        ];

        return ApiResponse::success($data);
    }


    public function matchPlayersAttendanceIndex(Request $request): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');

        return $this->attendanceReportService->matchPlayersAttendanceDatatables($team, $startDate, $endDate, $this->getLoggedCoachUser());
    }

    public function trainingPlayersAttendanceIndex(Request $request): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');

        return $this->attendanceReportService->trainingPlayersAttendanceDatatables($team, $startDate, $endDate, $this->getLoggedCoachUser());
    }


    public function matchesAttendanceIndex(Request $request): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');

        return $this->attendanceReportService->matchIndex($startDate, $endDate, $team, $this->getLoggedCoachUser());
    }
    public function trainingAttendanceIndex(Request $request): JsonResponse
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');

        return $this->attendanceReportService->trainingIndex($startDate, $endDate, $team, $this->getLoggedCoachUser());
    }


    public function show(Player $player){
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();
        return view('pages.academies.reports.attendances.player-detail', [
            'player' => $player,
            'playerMatchIllness' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness'),
            'playerMatchOthers' => $this->attendanceReportService->getPlayerAttendance($player, 'Others'),
            'playerMatchRequiredAction' => $this->attendanceReportService->getPlayerAttendance($player, 'Required Action'),
            'playerMatchInjured' => $this->attendanceReportService->getPlayerAttendance($player, 'Injured'),
            'playerMatchAttended' => $this->attendanceReportService->getPlayerAttendance($player, 'Attended'),
            'playerMatchTotalAbsent' => $this->attendanceReportService->getTotalAbsent($player),
            'playerTrainingIllness' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness', isMatch: false),
            'playerTrainingOthers' => $this->attendanceReportService->getPlayerAttendance($player, 'Others', isMatch: false),
            'playerTrainingRequiredAction' => $this->attendanceReportService->getPlayerAttendance($player, 'Required Action', isMatch: false),
            'playerTrainingInjured' => $this->attendanceReportService->getPlayerAttendance($player, 'Injured', isMatch: false),
            'playerTrainingAttended' => $this->attendanceReportService->getPlayerAttendance($player, 'Attended', isMatch: false),
            'playerTrainingTotalAbsent' => $this->attendanceReportService->getTotalAbsent($player, isMatch: false),
            'playerMatchIllnessThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness', $startDate, $endDate),
            'playerMatchOthersThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Others', $startDate, $endDate),
            'playerMatchRequiredActionThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Required Action', $startDate, $endDate),
            'playerMatchInjuredThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Injured', $startDate, $endDate),
            'playerMatchAttendedThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Attended', $startDate, $endDate),
            'playerMatchTotalAbsentThisMonth' => $this->attendanceReportService->getTotalAbsent($player, $startDate, $endDate),
            'playerTrainingIllnessThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness', $startDate, $endDate, false),
            'playerTrainingOthersThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Others', $startDate, $endDate, false),
            'playerTrainingRequiredActionThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Required Action', $startDate, $endDate, false),
            'playerTrainingInjuredThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Injured', $startDate, $endDate, false),
            'playerTrainingAttendedThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Attended', $startDate, $endDate, false),
            'playerTrainingTotalAbsentThisMonth' => $this->attendanceReportService->getTotalAbsent($player, $startDate, $endDate, false),
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
            'playerMatchIllness' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness'),
            'playerMatchOthers' => $this->attendanceReportService->getPlayerAttendance($player, 'Others'),
            'playerMatchRequiredAction' => $this->attendanceReportService->getPlayerAttendance($player, 'Required Action'),
            'playerMatchInjured' => $this->attendanceReportService->getPlayerAttendance($player, 'Injured'),
            'playerMatchAttended' => $this->attendanceReportService->getPlayerAttendance($player, 'Attended'),
            'playerMatchTotalAbsent' => $this->attendanceReportService->getTotalAbsent($player),
            'playerTrainingIllness' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness', isMatch: false),
            'playerTrainingOthers' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness', isMatch: false),
            'playerTrainingRequiredAction' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness', isMatch: false),
            'playerTrainingInjured' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness', isMatch: false),
            'playerTrainingAttended' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness', isMatch: false),
            'playerTrainingTotalAbsent' => $this->attendanceReportService->getTotalAbsent($player, isMatch: false),
            'playerMatchIllnessThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness', $startDate, $endDate),
            'playerMatchOthersThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Others', $startDate, $endDate),
            'playerMatchRequiredActionThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Required Action', $startDate, $endDate),
            'playerMatchInjuredThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Injured', $startDate, $endDate),
            'playerMatchAttendedThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Attended', $startDate, $endDate),
            'playerMatchTotalAbsentThisMonth' => $this->attendanceReportService->getTotalAbsent($player, $startDate, $endDate),
            'playerTrainingIllnessThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Illness', $startDate, $endDate, false),
            'playerTrainingOthersThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Others', $startDate, $endDate, false),
            'playerTrainingRequiredActionThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Required Action', $startDate, $endDate, false),
            'playerTrainingInjuredThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Injured', $startDate, $endDate, false),
            'playerTrainingAttendedThisMonth' => $this->attendanceReportService->getPlayerAttendance($player, 'Attended', $startDate, $endDate, false),
            'playerTrainingTotalAbsentThisMonth' => $this->attendanceReportService->getTotalAbsent($player, $startDate, $endDate, false),
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
