<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\PlayerPosition;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PlayerPositionRepository
{
    protected PlayerPosition $playerPosition;
    public function __construct(PlayerPosition $playerPosition)
    {
        $this->playerPosition = $playerPosition;
    }

    public function getAll()
    {
        return $this->playerPosition->all();
    }

    public function find($id)
    {
        return $this->playerPosition->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->playerPosition->create($data);
    }

    public function update($id, array $data)
    {
        $post = $this->find($id);
        $post->update($data);
        return $post;
    }

    public function delete($id)
    {
        $post = $this->find($id);
        $post->delete();
        return $post;
    }
}
