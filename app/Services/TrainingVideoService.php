<?php

namespace App\Services;

use App\Models\Player;
use App\Models\TrainingVideo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TrainingVideoService extends Service
{
    public function index(){
        return TrainingVideo::paginate(16);
    }

    public function players(TrainingVideo $trainingVideo){
        $data = $trainingVideo->players()->get();
        return Datatables::of($data)
            ->addColumn('action', function ($item) use ($trainingVideo) {
                return '<div class="btn-toolbar" role="toolbar">
                             <a class="btn btn-sm btn-outline-secondary mr-1" id="'.$item->id.'" href="" data-toggle="tooltip" data-placement="bottom" title="View Player">
                                <span class="material-icons">visibility</span>
                             </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary deletePlayer" id="' . $item->id . '" data-toggle="tooltip" data-placement="bottom" title="Remove Player">
                                <span class="material-icons">delete</span>
                            </button>
                        </div>';
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
                                            <p class="mb-0"><strong class="js-lists-values-lead">' . $item->user->firstName . ' '.$item->user->lastName.'</strong></p>
                                            <small class="js-lists-values-email text-50">' . $item->position->name . '</small>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            })
            ->editColumn('progress', function ($item) {
                return $item->pivot->progress .'%';
            })
            ->editColumn('assignedAt', function ($item) {
                $date = date('M d, Y ~ h:i A', strtotime($item->pivot->created_at));
                return $date;
            })
            ->editColumn('status', function ($item) {
                if ($item->pivot->status == 'Completed') {
                    return '<span class="badge badge-pill badge-success">Completed</span>';
                } elseif ($item->pivot->status == 'On Progress') {
                    return '<span class="badge badge-pill badge-warning">On Progress</span>';
                }
            })
            ->rawColumns(['action','name','progress','assignedAt', 'status'])
            ->addIndexColumn()
            ->make();
    }

    public function getTotalDuration(TrainingVideo $trainingVideo){
        return $this->secondToMinute($trainingVideo->lessons()->sum('totalDuration'));
    }

    public function store(array $data, $userId){
        $data['previewPhoto'] = $this->storeImage($data, 'previewPhoto', 'assets/training-videos', 'images/video-preview.png');
        $data['userId'] = $userId;

        return TrainingVideo::create($data);
    }

    public function update(array $data, TrainingVideo $trainingVideo){
        if (array_key_exists('previewPhoto', $data)){
            $this->deleteImage($trainingVideo->previewPhoto);
            $data['previewPhoto'] = $data['previewPhoto']->store('assets/training-videos', 'public');
        }else{
            $data['previewPhoto'] = $trainingVideo->previewPhoto;
        }

        return $trainingVideo->update($data);
    }

    public function publish(TrainingVideo $trainingVideo)
    {
        return $trainingVideo->update(['status' => '1']);
    }

    public function unpublish(TrainingVideo $trainingVideo)
    {
        return $trainingVideo->update(['status' => '0']);
    }

    public function updatePlayer(array $data, TrainingVideo $trainingVideo){
        $trainingVideo->players()->attach($data['players']);
        foreach($trainingVideo->lessons as $lesson){
            $lesson->players()->attach($data['players']);
        }
        return $trainingVideo;
    }

    public function removePlayer(TrainingVideo $trainingVideo, Player $player){
        $trainingVideo->players()->detach($player);
        foreach($trainingVideo->lessons as $lesson){
            $lesson->players()->detach($player);
        }
        return $trainingVideo;
    }

    public function destroy(TrainingVideo $trainingVideo){
        $trainingVideo->players()->detach();
        $this->deleteImage($trainingVideo->previewPhoto);
        $trainingVideo->delete();
        return $trainingVideo;
    }
}
