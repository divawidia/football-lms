<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            $this->eventScheduleService->dataTablesTraining();
        }

        $trainings = $this->eventScheduleService->indexTraining();
        $events = [];
        foreach ($trainings as $training) {
            $events[] = [
                'title' => $training->eventName.' - '.$training->teams[0]->teamName,
                'start' => $training->tanggal - $training->startTime,
                'end' => $training->tanggal - $training->endTime,
                'className' => 'bg-warning'
            ];
        }

        return view('pages.admins.academies.schedules.trainings.index', [
            'events' => $events
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teams = Team::where('teamSide', 'Academy Team')->get();
        return view('pages.admins.academies.schedules.trainings.create', [
            'teams' => $teams,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrainingScheduleRequest $request)
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
