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
        if (\request()->ajax()){
            return $this->attendanceReportService->dataTablesMatch($player);
        }
    }

    public function trainingTable(Player $player){
        if (\request()->ajax()){
            return $this->attendanceReportService->dataTablesTraining($player);
        }
    }
}
