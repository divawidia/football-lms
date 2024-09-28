<?php

namespace App\Services;

use App\Models\TrainingVideo;

class TrainingVideoService
{
    public function index(){
        return TrainingVideo::all();
    }

    public function store(array $data, $userId){
        if (array_key_exists('previewPhoto', $data)){
            $data['previewPhoto'] =$data['previewPhoto']->store('assets/training-videos', 'public');
        }else{
            $data['previewPhoto'] = 'images/video-preview.png';
        }
        $data['totalLesson'] = 0;
        $data['totalMinute'] = 0;
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
}
