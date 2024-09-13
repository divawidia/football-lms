<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingScheduleRequest;
use App\Models\Coach;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\Team;
use App\Services\EventScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class EventScheduleController extends Controller
{
    private EventScheduleService $eventScheduleService;
    public function __construct(EventScheduleService $eventScheduleService)
    {
        $this->eventScheduleService = $eventScheduleService;
    }
    /**
     * Display a listing of the resource.
     */
    public function indexTraining()
    {
        if (request()->ajax()){
            return $this->eventScheduleService->dataTablesTraining();
        }

        $events = $this->eventScheduleService->trainingCalendar();

        return view('pages.admins.academies.schedules.trainings.index', [
            'events' => $events
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createTraining()
    {
        return view('pages.admins.academies.schedules.trainings.create', [
            'teams' => $this->eventScheduleService->getAcademyTeams(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeTraining(TrainingScheduleRequest $request)
    {
        $data = $request->validated();
        $userId = Auth::user()->id;
        $this->eventScheduleService->storeTraining($data, $userId);

        $text = 'Training schedule successfully added!';
        Alert::success($text);
        return redirect()->route('training-schedules.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventSchedule $schedule)
    {
        $totalParticipant = count($schedule->players) + count($schedule->coaches);

        $playerAttended = $schedule->players()
            ->where('attendanceStatus', 'Attended')
            ->get();
        $playerDidntAttend = $schedule->players()
            ->where('attendanceStatus', 'Illness')
            ->orWhere('attendanceStatus', 'Injured')
            ->orWhere('attendanceStatus', 'Other')
            ->get();
        $playerIllness = $schedule->players()
            ->where('attendanceStatus', 'Illness')
            ->get();
        $playerInjured = $schedule->players()
            ->where('attendanceStatus', 'Injured')
            ->get();
        $playerOther = $schedule->players()
            ->where('attendanceStatus', 'Other')
            ->get();
        $coachAttended = $schedule->coaches()
            ->where('attendanceStatus', 'Attended')
            ->get();
        $coachDidntAttend = $schedule->coaches()
            ->where('attendanceStatus', 'Illness')
            ->where('attendanceStatus', 'Injured')
            ->where('attendanceStatus', 'Other')
            ->get();
        $coachIllness = $schedule->coaches()
            ->where('attendanceStatus', 'Illness')
            ->get();
        $coachInjured = $schedule->coaches()
            ->where('attendanceStatus', 'Injured')
            ->get();
        $coachOther = $schedule->coaches()
            ->where('attendanceStatus', 'Other')
            ->get();

        $totalAttend = count($playerAttended) + count($coachAttended);
        $totalDidntAttend = count($playerDidntAttend) + count($coachDidntAttend);
        $totalIllness = count($playerIllness) + count($coachIllness);
        $totalInjured = count($playerInjured) + count($coachInjured);
        $totalOthers = count($playerOther) + count($coachOther);

        return view('pages.admins.academies.schedules.trainings.detail', [
            'totalParticipant' => $totalParticipant,
            'totalAttend' => $totalAttend,
            'totalDidntAttend' => $totalDidntAttend,
            'totalIllness' => $totalIllness,
            'totalInjured' => $totalInjured,
            'totalOthers' => $totalOthers,
            'data' => $schedule,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editTraining(EventSchedule $schedule)
    {
        return view('pages.admins.academies.schedules.trainings.edit', [
            'teams' => $this->eventScheduleService->getAcademyTeams(),
            'data' => $schedule
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateTraining(TrainingScheduleRequest $request, EventSchedule $schedule)
    {
        $data = $request->validated();
        $this->eventScheduleService->updateTraining($data, $schedule);

        $text = 'Schedule successfully updated!';
        Alert::success($text);
        return redirect()->route('training-schedules.index');
    }
    public function activate(EventSchedule $schedule)
    {
        $this->eventScheduleService->activate($schedule);

        $text = 'Schedule status successfully updated!';
        Alert::success($text);
        return redirect()->route('training-schedules.show', $schedule->id);
    }

    public function deactivate(EventSchedule $schedule)
    {
        $this->eventScheduleService->deactivate($schedule);

        $text = 'Schedule status successfully updated!';
        Alert::success($text);
        return redirect()->route('training-schedules.show', $schedule->id);
    }

    public function getPlayerAttendance(EventSchedule $schedule, Player $player){
        $data = $this->eventScheduleService->getPlayerAttendance($schedule, $player);

        if (request()->ajax()) {
            if ($data) {
                return response()->json([
                    'status' => '200',
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'status' => '404',
                    'message' => 'Data not found!'
                ]);
            }
        } else {
            abort(404);
        }
    }

    public function getCoachAttendance(EventSchedule $schedule, Coach $coach){
        $data = $this->eventScheduleService->getCoachAttendance($schedule, $coach);

        if (request()->ajax()) {
            if ($data) {
                return response()->json([
                    'status' => '200',
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'status' => '404',
                    'message' => 'Data not found!'
                ]);
            }
        } else {
            abort(404);
        }
    }

    public function updatePlayerAttendance(Request $request, EventSchedule $schedule, Player $player)
    {
        $data = $request->validate([
            'attendanceStatus' => ['required', Rule::in('Attended', 'Illness', 'Injured', 'Other')],
            'note' => ['nullable', 'string']
        ]);
        $attendance = $this->eventScheduleService->updatePlayerAttendanceStatus($data, $schedule, $player);
        return response()->json($attendance);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventSchedule $schedule)
    {
        $this->eventScheduleService->destroy($schedule);

        return response()->json(['success' => true]);
    }
}
