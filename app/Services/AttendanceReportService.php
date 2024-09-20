<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\Player;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AttendanceReportService extends Service
{
    public function attendanceDatatables(){
        $query = Player::all();
        return Datatables::of($query)
            ->addColumn('action', function ($item) {
                return '<a class="btn btn-sm btn-outline-secondary" href="' . route('attendance-report.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View player attendance detail">
                            <span class="material-icons">visibility</span>
                        </a>';
            })
            ->editColumn('teams', function ($item) {
                $playerTeam = '';
                if(count($item->teams) === 0){
                    $playerTeam = 'No Team';
                }else{
                    foreach ($item->teams as $team){
                        $playerTeam .= '<span class="badge badge-pill badge-danger">'.$team->teamName.'</span>';
                    }
                }
                return $playerTeam;
            })
            ->editColumn('name', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                             style="white-space: nowrap;">
                            <div class="avatar avatar-sm mr-8pt">
                                <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->user->foto) . '" alt="profile-pic"/>
                            </div>
                            <div class="media-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex d-flex flex-column">
                                        <p class="mb-0"><strong class="js-lists-values-lead">'. $item->user->firstName .' '. $item->user->lastName .'</strong></p>
                                        <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                    </div>
                                </div>

                            </div>
                        </div>';
            })
            ->addColumn('totalEvent', function ($item){
                return count($item->schedules);
            })
            ->addColumn('match', function ($item){
                $match = $item->schedules()->where('eventType', 'Match')->get();
                return count($match);
            })
            ->addColumn('training', function ($item){
                $match = $item->schedules()->where('eventType', 'Training')->get();
                return count($match);
            })
            ->addColumn('attended', function ($item){
                $attended = $item->schedules()->where('attendanceStatus', 'Attended')->get();
                $totalAttended = count($attended);
                $totalEvent = count($item->schedules);
                if ($totalEvent == 0){
                    return 'No event yet';
                }else{
                    $percentage = $totalAttended/count($item->schedules)*100;
                    return $totalAttended . ' ('.round($percentage, 1).'%)';
                }
            })
            ->addColumn('absent', function ($item){
                $illness = $item->schedules()
                    ->where('attendanceStatus', 'Illness')
                    ->get();
                $injured = $item->schedules()
                    ->where('attendanceStatus', 'Injured')
                    ->get();
                $others = $item->schedules()
                    ->where('attendanceStatus', 'Others')
                    ->get();

                $totalDidntAttended = count($illness) + count($injured) + count($others);
                $totalEvent = count($item->schedules);
                if ($totalEvent == 0){
                    return 'No event yet';
                }else{
                    $percentage = $totalDidntAttended/count($item->schedules)*100;
                    return $totalDidntAttended . ' ('.round($percentage, 1).'%)';
                }
            })
            ->addColumn('illness', function ($item){
                $didntAttend = $item->schedules()
                    ->where('attendanceStatus', 'Illness')
                    ->get();
                return count($didntAttend);
            })
            ->addColumn('injured', function ($item){
                $didntAttend = $item->schedules()
                    ->where('attendanceStatus', 'Injured')
                    ->get();
                return count($didntAttend);
            })
            ->addColumn('others', function ($item){
                $didntAttend = $item->schedules()
                    ->where('attendanceStatus', 'Others')
                    ->get();
                return count($didntAttend);
            })
            ->rawColumns(['action','teams', 'name','totalEvent', 'match', 'training', 'attended', 'absent', 'illness', 'injured', 'others'])
            ->make();
    }

    public function index(){
        $mostAttended = Player::with('schedules', 'user')
        ->withCount(['schedules', 'schedules as attended_count' => function ($query){
            $query->where('attendanceStatus', 'Attended');
        }])->orderBy('attended_count', 'desc')->first();

        $mostAttendedPercentage = $mostAttended->attended_count / count($mostAttended->schedules) * 100;
        $mostAttendedPercentage = round($mostAttendedPercentage, 1);

        $mostDidntAttend = Player::with('schedules', 'user')
            ->withCount(['schedules', 'schedules as didnt_attended_count' => function ($query){
                $query->where('attendanceStatus', 'Illness')
                    ->orWhere('attendanceStatus', 'Injured')
                    ->orWhere('attendanceStatus', 'Other');
            }])->orderBy('didnt_attended_count', 'desc')->first();

        $mostDidntAttendPercentage = $mostDidntAttend->didnt_attended_count / count($mostDidntAttend->schedules) * 100;
        $mostDidntAttendPercentage = round($mostDidntAttendPercentage, 1);

        return compact('mostAttended', 'mostDidntAttend', 'mostAttendedPercentage', 'mostDidntAttendPercentage');
    }

    public function show(Player $player){
        $playerAttended = $player->schedules()
            ->where('attendanceStatus', 'Attended')
            ->get();

        $playerIllness = $player->schedules()
            ->where('attendanceStatus', 'Illness')
            ->get();
        $playerInjured = $player->schedules()
            ->where('attendanceStatus', 'Injured')
            ->get();
        $playerOther = $player->schedules()
            ->where('attendanceStatus', 'Other')
            ->get();

        $totalAttended = count($playerAttended);
        $totalIllness = count($playerIllness);
        $totalInjured = count($playerInjured);
        $totalOther = count($playerOther);

        return compact('totalAttended', 'totalIllness', 'totalInjured', 'totalOther');
    }

    public function dataTablesTraining(Player $player){
        return Datatables::of($player->schedules()->where('eventType', 'Training')->get())
            ->addColumn('action', function ($item) {
                return '<a class="btn btn-sm btn-outline-secondary" href="' . route('training-schedules.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View training detail">
                            <span class="material-icons">visibility</span>
                        </a>';
            })
            ->editColumn('team', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[0]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[0]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[0]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('date', function ($item) {
                $date = date('M d, Y', strtotime($item->date));
                $startTime = date('h:i A', strtotime($item->startTime));
                $endTime = date('h:i A', strtotime($item->endTime));
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    return '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    return '<span class="badge badge-pill badge-danger">Ended</span>';
                }
            })
            ->editColumn('attendanceStatus', function ($item) {
                if ($item->pivot->attendanceStatus == 'Attended') {
                    return '<span class="badge badge-pill badge-success">Attended</span>';
                } else {
                    return '<span class="badge badge-pill badge-danger">'.$item->pivot->attendanceStatus.'</span>';
                }
            })
            ->editColumn('note', function ($item) {
                if ($item->pivot->note == null) {
                    return 'No note added';
                } else {
                    return $item->pivot->note;
                }
            })
            ->editColumn('last_updated', function ($item) {
                return date('M d, Y ~ h:i A', strtotime($item->pivot->updated_at));
            })
            ->rawColumns(['action','team','date','status', 'attendanceStatus', 'last_updated', 'note'])
            ->make();
    }

    public function dataTablesMatch(Player $player){
        return Datatables::of($player->schedules()->where('eventType', 'Match')->get())
            ->addColumn('action', function ($item) {
                return '<a class="btn btn-sm btn-outline-secondary" href="' . route('match-schedules.show', $item->id) . '" data-toggle="tooltip" data-placement="bottom" title="View match detail">
                            <span class="material-icons">visibility</span>
                        </a>';
            })
            ->editColumn('team', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[0]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[0]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[0]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('opponentTeam', function ($item) {
                return '
                        <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->teams[1]->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->teams[1]->teamName . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->teams[1]->ageGroup.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('competition', function ($item) {
                if ($item->competition){
                    $competition = '
                            <div class="media flex-nowrap align-items-center"
                                 style="white-space: nowrap;">
                                <div class="avatar avatar-sm mr-8pt">
                                    <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($item->competition->logo) . '" alt="profile-pic"/>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex d-flex flex-column">
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->competition->name . '</strong></p>
                                            <small class="js-lists-values-email text-50">'.$item->competition->type.'</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                }else{
                    $competition = 'No Competition';
                }
                return $competition;
            })
            ->editColumn('date', function ($item) {
                $date = date('M d, Y', strtotime($item->date));
                $startTime = date('h:i A', strtotime($item->startTime));
                $endTime = date('h:i A', strtotime($item->endTime));
                return $date.' ('.$startTime.' - '.$endTime.')';
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    return '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    return '<span class="badge badge-pill badge-danger">Ended</span>';
                }
            })
            ->editColumn('attendanceStatus', function ($item) {
                if ($item->pivot->attendanceStatus == 'Attended') {
                    return '<span class="badge badge-pill badge-success">Attended</span>';
                } else {
                    return '<span class="badge badge-pill badge-danger">'.$item->pivot->attendanceStatus.'</span>';
                }
            })
            ->editColumn('note', function ($item) {
                if ($item->pivot->note == null) {
                    return 'No note added';
                } else {
                    return $item->pivot->note;
                }
            })
            ->editColumn('last_updated', function ($item) {
                return date('M d, Y ~ h:i A', strtotime($item->pivot->updated_at));
            })
            ->rawColumns(['action','team', 'competition','opponentTeam','date','status', 'attendanceStatus', 'last_updated', 'note'])
            ->make();
    }
}
