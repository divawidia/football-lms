<?php

namespace App\Services;

use App\Models\EventSchedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class EventScheduleService extends Service
{
    public function index(): Collection
    {
        return EventSchedule::all();
    }

    public function storeTraining(array $data, $userId){
        $data['userId'] = $userId;
        $data['eventType'] = 'Training';
        $data['status'] = '1';
        $schedule =  EventSchedule::create($data);
        $schedule->teams()->attach($data['teamId']);
        return $schedule;
    }


    public function storeTraining(array $data, $userId){
        $data['userId'] = $userId;
        $data['eventType'] = 'Training';
        $data['status'] = '1';
        $schedule =  EventSchedule::create($data);
        $schedule->teams()->attach($data['teamId']);
        return $schedule;
    }


}
