<?php

namespace App\Services;

use App\Models\TrainingVideo;
use App\Models\TrainingVideoLesson;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TrainingVideoLessonService extends Service
{
    public function index(TrainingVideo $trainingVideo){
        $data = $trainingVideo->lessons;
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($trainingVideo) {
                if ($item->status == '1') {
                    $statusButton = '<form action="' . route('training-videos.lessons-unpublish', ['trainingVideo'=>$trainingVideo->id,'lesson'=>$item->id]) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="bottom" title="Unpublish lesson">
                                            <span class="material-icons">block</span>
                                        </button>
                                    </form>';
                } else {
                    $statusButton = '<form action="' . route('training-videos.lessons-publish', ['trainingVideo'=>$trainingVideo->id,'lesson'=>$item->id]) . '" method="POST">
                                        ' . method_field("PATCH") . '
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="tooltip" data-placement="bottom" title="Publish lesson">
                                            <span class="material-icons">check_circle</span>
                                        </button>
                                    </form>';
                }
                return '<div class="btn-toolbar" role="toolbar">
                            <button class="btn btn-sm btn-outline-secondary mr-1 editLesson" id="'.$item->id.'" type="button" data-toggle="tooltip" data-placement="bottom" title="Edit lesson">
                                <span class="material-icons">edit</span>
                             </button>
                             <a class="btn btn-sm btn-outline-secondary mr-1" id="'.$item->id.'" href="" data-toggle="tooltip" data-placement="bottom" title="View lesson">
                                <span class="material-icons">visibility</span>
                             </a>
                             '.$statusButton.'
                            <button type="button" class="btn btn-sm btn-outline-secondary deleteLesson" id="' . $item->id . '" data-toggle="tooltip" data-placement="bottom" title="Edit lesson">
                                <span class="material-icons">delete</span>
                            </button>
                        </div>';
            })
            ->editColumn('title', function ($item) {
                return '<p class="mb-0"><strong class="js-lists-values-lead">' . $item->lessonTitle . '</strong></p>';
            })
            ->editColumn('totalDuration', function ($item) {
                return $this->secondToMinute($item->totalDuration);
            })
            ->editColumn('description', function ($item) {
                if ($item->description == null){
                    $description = 'No description yet';
                }else{
                    $description = Str::limit($item->description, 150);
                }
                return $description;
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
            ->addIndexColumn()
            ->make();
    }
    public function store(array $data, TrainingVideo $trainingVideo){
        $data['trainingVideoId'] = $trainingVideo->id;
        $players = $trainingVideo->players()->select('playerId')->get();
        $lesson = TrainingVideoLesson::create($data);
        $lesson->players()->attach($players);
        return $lesson;
    }

    public function update(array $data, TrainingVideoLesson $trainingVideoLesson){
        return $trainingVideoLesson->update($data);
    }

    public function publish(TrainingVideoLesson $trainingVideoLesson)
    {
        return $trainingVideoLesson->update(['status' => '1']);
    }

    public function unpublish(TrainingVideoLesson $trainingVideoLesson)
    {
        return $trainingVideoLesson->update(['status' => '0']);
    }

    public function destroy(TrainingVideoLesson $trainingVideoLesson)
    {
        return $trainingVideoLesson->delete();
    }
}
