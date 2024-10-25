<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\EventSchedule;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;

class PlayerRepository
{
    protected Player $player;
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getAll()
    {
        return $this->player->all();
    }

    public function getCoachsPLayers($teams)
    {
        return $this->player->whereHas('teams', function ($q) use ($teams){
            $q->where('teamId', $teams[0]->id);
            // if teams are more than 1 then iterate more
            if (count($teams)>1){
                for ($i = 1; $i < count($teams); $i++){
                    $q->orWhere('teamId', $teams[$i]->id);
                }
            }
        })->get();
    }

    public function find($id)
    {
        return $this->player->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->player->create($data);
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
