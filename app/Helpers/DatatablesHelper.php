<?php

namespace App\Helpers;

use App\Services\Service;
use Illuminate\Support\Facades\Storage;

class DatatablesHelper extends Service
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

    public function attendanceStatus($status): string
    {
        if ($status == 'Attended') {
            $badge = '<span class="badge badge-pill badge-success">'.$status.'</span>';
        }else {
            $badge = '<span class="badge badge-pill badge-danger">'.$status.'</span>';
        }
        return $badge;
    }

    public function eventStatus($status): string
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

    public function invoiceStatus($status)
    {
        if ($status == 'Past Due') {
            $status = '<span class="badge badge-pill badge-warning">'.$status .'</span>';
        } elseif ($status == 'Open') {
            $status = '<span class="badge badge-pill badge-info">'.$status .'</span>';
        } elseif ($status == 'Paid') {
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

    public function competitionStartEndDate($data)
    {
        $startDate = $this->convertToDate($data->startDate);
        $endDate = $this->convertToDate($data->endDate);
        return $startDate.' - '.$endDate;
    }
    public function name($image, $title, $subtitle, $showRoute = null)
    {
        if ($showRoute != null) {
            $text = '<a href="'.$showRoute.'">
                        <p class="mb-0"><strong class="js-lists-values-lead">' . $title . '</strong></p>
                    </a>';
        } else {
            $text = '<p class="mb-0"><strong class="js-lists-values-lead">' . $title . '</strong></p>';
        }

        if ($image != null) {
            $img = '<img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url($image) . '" alt="profile-pic"/>';
        } else {
            $img = '<img class="rounded-circle header-profile-user img-object-fit-cover" width="40" height="40" src="' . Storage::url('/images/undefined-user.png') . '" alt="profile-pic"/>';
        }
        return '<div class="media flex-nowrap align-items-center" style="white-space: nowrap;">
                    <div class="avatar avatar-sm mr-8pt">
                        '.$img.'
                    </div>
                    <div class="media-body">
                        <div class="d-flex align-items-center">
                            <div class="flex d-flex flex-column">
                                '.$text.'
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
