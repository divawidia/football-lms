<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Player;
use App\Models\Tax;
use App\Models\TrainingVideo;
use App\Models\User;

class TrainingVideoRepository
{
    protected TrainingVideo $trainingVideo;
    public function __construct(TrainingVideo $trainingVideo)
    {
        $this->trainingVideo = $trainingVideo;
    }

    public function getAll()
    {
        return $this->trainingVideo->all();
    }

    public function paginate($count)
    {
        return $this->trainingVideo->paginate($count);
    }

    public function playerPaginate(Player $player, $count)
    {
        return $player->trainingVideos()->where('training_videos.status', '1')->paginate($count);
    }

    public function find($id)
    {
        return $this->trainingVideo->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->trainingVideo->create($data);
    }

    public function update(array $data)
    {
        return $this->trainingVideo->update($data);
    }

    public function delete()
    {
        return $this->trainingVideo->delete();
    }
}
