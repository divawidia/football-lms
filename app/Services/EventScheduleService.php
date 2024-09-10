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
        $query = EventSchedule::all();
        return $query;
    }


}
