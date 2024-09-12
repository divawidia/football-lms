<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingScheduleRequest;
use App\Models\EventSchedule;
use App\Models\Team;
use App\Services\EventScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function show(string $id)
    {
        //
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
    public function update(TrainingScheduleRequest $request, EventSchedule $schedule)
    {
        $data = $request->validated();
        $this->eventScheduleService->update($data, $schedule);

        $text = 'Schedule successfully updated!';
        Alert::success($text);
        return redirect()->route('training-schedules.index');
    }
    public function activate(EventSchedule $schedule)
    {
        $this->eventScheduleService->activate($schedule);

        $text = 'Schedule status successfully updated!';
        Alert::success($text);
        return redirect()->route('competition-managements.show', $schedule->id);
    }

    public function deactivate(EventSchedule $schedule)
    {
        $this->eventScheduleService->deactivate($schedule);

        $text = 'Schedule status successfully updated!';
        Alert::success($text);
        return redirect()->route('competition-managements.show', $schedule->id);
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
