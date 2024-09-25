<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerformanceReportController extends Controller
{
    public function index(){
//        if (\request()->ajax()){
//            return $this->attendanceReportService->attendanceDatatables();
//        }
//        $data = $this->attendanceReportService->index();

        return view('pages.admins.academies.reports.performance.index');
    }
}
