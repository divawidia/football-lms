<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use App\Repository\TeamRepository;
use App\Services\AttendanceReportService;
use Exception;
use Hamcrest\Core\IsNot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    private AttendanceReportService $attendanceReportService;
    private TeamRepository $teamRepository;

    public function __construct(
        AttendanceReportService $attendanceReportService,
        TeamRepository $teamRepository
    )
    {
        $this->attendanceReportService = $attendanceReportService;
        $this->teamRepository = $teamRepository;
    }
    public function index(){
        if (isAllAdmin()){
            $data = null;
            $teams = $this->teamRepository->getByTeamside('Academy Team');
            $playerAttendanceDatatablesRoute = url()->route('admin.attendance-report.index');
        }
        elseif (isCoach()){
            $coach = $this->getLoggedCoachUser();
            $data = null;
            $teams = $coach->teams;
            $playerAttendanceDatatablesRoute = url()->route('coach.attendance-report.index');
        }
        else {
            $player = $this->getLoggedPLayerUser();
            $teams = $player->teams;
            $data = $this->attendanceReportService->show($player);
            return view('pages.academies.reports.attendances.player-detail', [
                'data' => $data,
                'player' => $player
            ]);
        }

        if (\request()->ajax()) {
            $startDate = \request()->input('startDate');
            $endDate = \request()->input('endDate');
            $team = \request()->input('team');
            $eventType = \request()->input('eventType');
            if ($team == null and $this->getLoggedUser()->getRoleNames() == isCoach()) {
                $coach = $this->getLoggedCoachUser();
                $team = $coach->teams;
            } elseif ($team != null) {
                $team = $this->teamRepository->whereId($team);
            }
            $lineChart = $this->attendanceReportService->attendanceLineChart($startDate, $endDate, $team, $eventType);
            $doughnutChart = $this->attendanceReportService->attendanceDoughnutChart($startDate, $endDate, $team, $eventType);
            $mostAttendedPlayer = $this->attendanceReportService->mostAttendedPlayer($startDate, $endDate, $team, $eventType);
            $mostDidntAttendPlayer = $this->attendanceReportService->mostDidntAttendPlayer($startDate, $endDate, $team, $eventType);
            $events = $this->attendanceReportService->eventIndex($startDate, $endDate, $team, $eventType);

            $data = compact('lineChart', 'doughnutChart', 'mostAttendedPlayer', 'mostDidntAttendPlayer', 'events');

            return ApiResponse::success($data);
        }

        return view('pages.academies.reports.attendances.index', [
            'data' => $data,
            'teams' => $teams,
            'playerAttendanceDatatablesRoute' => $playerAttendanceDatatablesRoute,
        ]);
    }

    public function eventsIndex(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');
        $eventType = $request->input('eventType');
        return $this->attendanceReportService->eventIndex($startDate, $endDate, $team, $eventType);
    }

    public function adminIndex(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');
        $eventType = $request->input('eventType');
        return $this->attendanceReportService->attendanceDatatables($team, $startDate, $endDate, $eventType);
    }

    public function coachIndex(Request $request){
        $coach = $this->getLoggedCoachUser();
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $team = $request->input('team');
        $eventType = $request->input('eventType');
        return $this->attendanceReportService->coachAttendanceDatatables($coach, $team, $startDate, $endDate, $eventType);
    }

    public function show(Player $player){
        $data = $this->attendanceReportService->show($player);
        return view('pages.academies.reports.attendances.player-detail', [
            'data' => $data,
            'player' => $player
        ]);
    }

    public function matchDatatable(Player $player){
            return $this->attendanceReportService->dataTablesMatch($player);
    }

    public function trainingTable(Player $player){
            return $this->attendanceReportService->dataTablesTraining($player);
    }

    public function playerMatchHistories(){
        $player = $this->getLoggedPLayerUser();
        return $this->attendanceReportService->dataTablesMatch($player);
    }
    public function playerTrainingHistories(){
        $player = $this->getLoggedPLayerUser();
        return $this->attendanceReportService->dataTablesTraining($player);
    }
}
