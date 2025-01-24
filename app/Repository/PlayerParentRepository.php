<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\PlayerParrent;
use App\Models\PlayerPosition;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PlayerParentRepository
{
    protected PlayerParrent $playerParrent;
    public function __construct(PlayerParrent $playerParrent)
    {
        $this->playerParrent = $playerParrent;
    }

    public function getAll()
    {
        return $this->playerParrent->all();
    }

    public function find($id)
    {
        return $this->playerParrent->findOrFail($id);
    }

    public function create(array $data, $playerId)
    {
        return $this->playerParrent->create($data);
    }

    public function update($id, array $data)
    {
        $post = $this->find($id);
        $post->update($data);
        return $post;
    }

    public function changePassword($data, $userModel)
    {
        return $userModel->user()->update([
            'password' => bcrypt($data['password'])
        ]);
    }

    public function delete($id)
    {
        $post = $this->find($id);
        $post->delete();
        return $post;
    }
}
