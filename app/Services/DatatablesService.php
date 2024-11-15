<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\World;
use Yajra\DataTables\Facades\DataTables;

class DatatablesService extends Service
{
    public function activeNonactiveStatus($status)
    {
        $badge = '';
        if ($status == '1') {
            $badge = '<span class="badge badge-pill badge-success">Active</span>';
        }elseif ($status == '0'){
            $badge = '<span class="badge badge-pill badge-danger">Non-Active</span>';
        }
        return $badge;
    }

    public function fourTypeStatus($status)
    {
        if ($status == 'Scheduled') {
            $status = '<span class="badge badge-pill badge-warning">'.$status .'</span>';
        } elseif ($status == 'Ongoing') {
            $status = '<span class="badge badge-pill badge-info">'.$status .'</span>';
        } elseif ($status == 'Completed') {
            $status = '<span class="badge badge-pill badge-success">'.$status .'</span>';
        } else {
            $status = '<span class="badge badge-pill badge-danger">'.$status .'</span>';
        }
        return $status;
    }
    public function startEndDate($data)
    {
        $date = $this->convertToDate($data->date);
        $startTime = $this->convertToTime($data->startTime);
        $endTime = $this->convertToTime($data->endTime);
        return $date.' ('.$startTime.' - '.$endTime.')';
    }
    public function name($image, $title, $subtitle)
    {
        return '<div class="media flex-nowrap align-items-center" style="white-space: nowrap;">
                    <div class="avatar avatar-sm mr-8pt">
                        <img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($image) . '" alt="profile-pic"/>
                    </div>
                    <div class="media-body">
                        <div class="d-flex align-items-center">
                            <div class="flex d-flex flex-column">
                                <p class="mb-0"><strong class="js-lists-values-lead">' . $title . '</strong></p>
                                <small class="js-lists-values-email text-50">'.$subtitle.'</small>
                            </div>
                        </div>
                    </div>
                </div>';
    }
    public function buttonTooltips($route, string $tooltipsTitle, $icon)
    {
        return '<a class="btn btn-sm btn-outline-secondary" href="' . $route. '" data-toggle="tooltip" data-placement="bottom" title="'.$tooltipsTitle.'">
                        <span class="material-icons">
                            '.$icon.'
                        </span>
                  </a>';
    }
}
