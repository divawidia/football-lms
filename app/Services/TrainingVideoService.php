<?php

namespace App\Services;

use App\Models\Player;
use App\Models\TrainingVideo;

class TrainingVideoService extends Service
{
    public function index(){
        return TrainingVideo::paginate(16);
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

    public function assignPlayer(array $data, TrainingVideo $trainingVideo){
        return $trainingVideo->players()->attach($data);
    }

    public function removePlayer(TrainingVideo $trainingVideo, Player $player){
        return $trainingVideo->players()->detach($player);
    }

    public function destroy(TrainingVideo $trainingVideo){
        $trainingVideo->players()->detach();
        $this->deleteImage($trainingVideo->previewPhoto);
        return $trainingVideo->delete();
    }
}
