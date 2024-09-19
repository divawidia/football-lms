<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
