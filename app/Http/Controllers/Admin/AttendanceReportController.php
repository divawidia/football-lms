<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use App\Repository\TeamRepository;
use App\Services\AttendanceReportService;
use Exception;
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
            $teams = null;
            $player = $this->getLoggedPLayerUser();
            $data = $this->attendanceReportService->show($player);
            $playerAttendanceDatatablesRoute = null;
        }

        if (\request()->ajax()) {
            $startDate = \request()->input('startDate');
            $endDate = \request()->input('endDate');
            $team = \request()->input('team');
            $eventType = \request()->input('eventType');
            if ($team) {
                $team = $this->teamRepository->whereId($team);
                $lineChart = $this->attendanceReportService->attendanceLineChart($startDate, $endDate, $team, $eventType);
                $doughnutChart = $this->attendanceReportService->attendanceDoughnutChart($startDate, $endDate, $team, $eventType);
                $mostAttendedPlayer = $this->attendanceReportService->mostAttendedPlayer($startDate, $endDate, $team, $eventType);
                $mostDidntAttendPlayer = $this->attendanceReportService->mostDidntAttendPlayer($startDate, $endDate, $team, $eventType);
                $events = $this->attendanceReportService->eventIndex($startDate, $endDate, $team, $eventType);
            }
//            elseif ($player) {
//                $lineChart = $this->attendanceReportService->attendanceLineChart($startDate, $endDate, $player);
//                $doughnutChart = $this->attendanceReportService->attendanceDoughnutChart($startDate, $endDate, $player);
//                $mostAttendedPlayer = null;
//                $mostDidntAttendPlayer = null;
//            }
            else {
                $lineChart = $this->attendanceReportService->attendanceLineChart($startDate, $endDate, eventType: $eventType);
                $doughnutChart = $this->attendanceReportService->attendanceDoughnutChart($startDate, $endDate, eventType: $eventType);
                $mostAttendedPlayer = $this->attendanceReportService->mostAttendedPlayer($startDate, $endDate, eventType: $eventType);
                $mostDidntAttendPlayer = $this->attendanceReportService->mostDidntAttendPlayer($startDate, $endDate, eventType: $eventType);
                $events = $this->attendanceReportService->eventIndex($startDate, $endDate, eventType:  $eventType);
            }

            $data = compact('lineChart', 'doughnutChart', 'mostAttendedPlayer', 'mostDidntAttendPlayer', 'events');

            return ApiResponse::success($data);
        }

        return view('pages.academies.reports.attendances.index', [
            'data' => $data,
            'teams' => $teams,
            'playerAttendanceDatatablesRoute' => $playerAttendanceDatatablesRoute,
        ]);
    }

    public function eventsIndex()
    {
        if (request()->ajax()) {
            $startDate = request()->input('startDate');
            $endDate = request()->input('endDate');
            $team = request()->input('team');
            $eventType = request()->input('eventType');
            if ($team) {
                $team = $this->teamRepository->whereId($team);
                return $this->attendanceReportService->eventIndex($startDate, $endDate, $team, $eventType);
            }
            else {
                return $this->attendanceReportService->eventIndex($startDate, $endDate, eventType: $eventType);
            }
        }
    }

    public function adminIndex()
    {
        return $this->attendanceReportService->attendanceDatatables();
    }

    public function coachIndex(){
        $coach = $this->getLoggedCoachUser();
        return $this->attendanceReportService->coachAttendanceDatatables($coach);
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
