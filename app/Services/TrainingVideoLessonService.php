<?php

namespace App\Services;

use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TrainingVideoLessonService
{
    public function index(TrainingVideo $trainingVideo){
        $data = $trainingVideo->lessons;
        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('deactivate-lesson', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            <span class="material-icons">block</span> Deactivate Lesson
                                        </button>
                                    </form>';
                } else {
                    $statusButton = '<form action="' . route('activate-lesson', $item->id) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            <span class="material-icons">check_circle</span> Activate Lesson
                                        </button>
                                    </form>';
                }
                return '
                            <a class="btn btn-sm btn-outline-secondary mr-1" id="'.$item->id.'" href=""><span class="material-icons">edit</span> Edit Lesson</a>
                            ' . $statusButton . '
                            <button type="button" class="dropdown-item delete" id="' . $item->id . '">
                                <span class="material-icons">delete</span> Delete Lesson
                            </button>';
            })
            ->editColumn('title', function ($item) {
                return '<p class="mb-0"><strong class="js-lists-values-lead">' . $item->lessonTitle . '</strong></p>';
            })
            ->editColumn('totalMinutes', function ($item) {
                return '<p class="mb-0">' . $item->totalMinutes . 'm</p>';
            })
            ->editColumn('description', function ($item) {
                return Str::limit($item->description, 150);
            })
            ->editColumn('created_date', function ($item) {
                $date = date('M d, Y ~ h:i A', strtotime($item->created_at));
                return $date;
            })
            ->editColumn('last_updated', function ($item) {
                $date = date('M d, Y ~ h:i A', strtotime($item->updated_at));
                return $date;
            })
            ->editColumn('status', function ($item) {
                if ($item->status == '1') {
                    return '<span class="badge badge-pill badge-success">Active</span>';
                } elseif ($item->status == '0') {
                    return '<span class="badge badge-pill badge-danger">Non-Active</span>';
                }
            })
            ->rawColumns(['action','title','totalMinutes','description', 'created_date', 'last_updated', 'status'])
            ->make();
    }
    public function store(array $data, TrainingVideo $trainingVideo){
        $data['trainingVideoId'] = $trainingVideo->id;
        return TrainingVideoLesson::create($data);
    }

    public function update(array $data, TrainingVideoLesson $trainingVideoLesson){
        return $trainingVideoLesson->update($data);
    }

    public function destroy(TrainingVideoLesson $trainingVideoLesson)
    {
        return $trainingVideoLesson->delete();
    }
}
