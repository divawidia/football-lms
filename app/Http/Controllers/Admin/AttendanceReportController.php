<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    public function index(){
        return view('pages.admins.academies.reports.attendances.index');
    }
}
