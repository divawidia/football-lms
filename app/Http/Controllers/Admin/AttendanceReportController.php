<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\AttendanceReportService;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    private AttendanceReportService $attendanceReportService;

    public function __construct(AttendanceReportService $attendanceReportService)
    {
        $this->attendanceReportService = $attendanceReportService;
    }
    public function index(){
        if (isAllAdmin()){
            $data = $this->attendanceReportService->index();
            $lineChart = $this->attendanceReportService->attendanceLineChart();
            $doughnutChart = $this->attendanceReportService->attendanceDoughnutChart();
            $playerAttendanceDatatablesRoute = url()->route('admin.attendance-report.index');
        }
        elseif (isCoach()){
            $coach = $this->getLoggedCoachUser();
            $player = null;
            $data = $this->attendanceReportService->coachIndex($coach);
            $lineChart = $this->attendanceReportService->attendanceLineChart($player, $coach);
            $doughnutChart = $this->attendanceReportService->attendanceDoughnutChart($player, $coach);
            $playerAttendanceDatatablesRoute = url()->route('coach.attendance-report.index');
        }
        elseif (isPlayer()){
            $player = $this->getLoggedPLayerUser();
            $data = $this->attendanceReportService->show($player);
            $data['mostDidntAttend'] = null;
            $data['mostAttended'] = null;
            $data['mostAttendedPercentage'] = null;
            $data['mostDidntAttendPercentage'] = null;
            $lineChart = $this->attendanceReportService->attendanceLineChart($player);
            $doughnutChart = $this->attendanceReportService->attendanceDoughnutChart($player);
            $playerAttendanceDatatablesRoute = null;
        }

        return view('pages.academies.reports.attendances.index', [
            'data' => $data,
            'mostDidntAttend' => $data['mostDidntAttend'],
            'mostAttended' => $data['mostAttended'],
            'mostAttendedPercentage' => $data['mostAttendedPercentage'],
            'mostDidntAttendPercentage' => $data['mostDidntAttendPercentage'],
            'lineChart' => $lineChart,
            'doughnutChart' => $doughnutChart,
            'playerAttendanceDatatablesRoute' => $playerAttendanceDatatablesRoute,
            ]);
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
