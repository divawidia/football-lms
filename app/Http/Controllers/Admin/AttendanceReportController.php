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
        if (\request()->ajax()){
            return $this->attendanceReportService->attendanceDatatables();
        }
        $data = $this->attendanceReportService->index();

        return view('pages.admins.academies.reports.attendances.index', [
            'mostDidntAttend' => $data['mostDidntAttend'],
            'mostAttended' => $data['mostAttended'],
            'mostAttendedPercentage' => $data['mostAttendedPercentage'],
            'mostDidntAttendPercentage' => $data['mostDidntAttendPercentage'],
            'lineChart' => $this->attendanceReportService->attendanceLineChart(),
            'doughnutChart' => $this->attendanceReportService->attendanceDoughnutChart(),
        ]);
    }

    public function coachIndex(){
        $coach = $this->getLoggedCoachUser();
        if (\request()->ajax()){
            return $this->attendanceReportService->coachAttendanceDatatables($coach);
        }
        $data = $this->attendanceReportService->coachIndex($coach);

        return view('pages.admins.academies.reports.attendances.index', [
            'mostDidntAttend' => $data['mostDidntAttend'],
            'mostAttended' => $data['mostAttended'],
            'mostAttendedPercentage' => $data['mostAttendedPercentage'],
            'mostDidntAttendPercentage' => $data['mostDidntAttendPercentage'],
            'lineChart' => $this->attendanceReportService->coachAttendanceLineChart($coach),
            'doughnutChart' => $this->attendanceReportService->coachAttendanceDoughnutChart($coach),
        ]);
    }

    public function show(Player $player){
        $data = $this->attendanceReportService->show($player);
        return view('pages.admins.academies.reports.attendances.player-detail', [
            'totalAttended' => $data['totalAttended'],
            'totalIllness' => $data['totalIllness'],
            'totalInjured' => $data['totalInjured'],
            'totalOther' => $data['totalOther'],
            'player' => $player
        ]);
    }

    public function matchDatatable(Player $player){
            return $this->attendanceReportService->dataTablesMatch($player);
    }

    public function trainingTable(Player $player){
            return $this->attendanceReportService->dataTablesTraining($player);
    }
}
