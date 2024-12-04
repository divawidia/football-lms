<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;

class CoachRepository
{
    protected Coach $coach;
    protected CoachSpecialization $coachSpecialization;
    protected CoachCertification $coachCertification;

    public function __construct(Coach $coach, CoachSpecialization $coachSpecialization, CoachCertification $coachCertification)
    {
        $this->coach = $coach;
        $this->coachSpecialization = $coachSpecialization;
        $this->coachCertification = $coachCertification;
    }

    public function getAll()
    {
        return $this->coach->with('user', 'teams')->get();
    }
    public function getInArray($coachIds)
    {
        return $this->coach->whereIn('id', $coachIds)->get();
    }
    public function getCoachNotJoinSpecificTeam(Team $team)
    {
        return $this->coach->with('user')
            ->whereDoesntHave('teams', function (Builder $query) use ($team){
                $query->where('teamId', $team->id);
            })
            ->get();
    }
    public function getAllCoachSpecialization()
    {
        return $this->coachSpecialization->all();
    }
    public function getAllCoachCertification()
    {
        return $this->coachCertification->all();
    }

    public function find($id)
    {
        return $this->coach->findOrFail($id);
    }

    public function create(array $data)
    {
        $coach = $this->coach->create($data);
        if (array_key_exists('team',$data)){
            $coach->teams()->attach($data['team']);
        }
        return $coach;
    }

    public function update(Coach $coach, array $data)
    {
        $coach->update($data);
        $coach->user->update($data);
        return $coach;
    }

    public function activate(Coach $coach)
    {
        return $coach->user()->update(['status' => '1']);
    }

    public function deactivate(Coach $coach)
    {
        return $coach->user()->update(['status' => '0']);
    }

    public function changePassword(array $data, Coach $coach)
    {
        return $coach->user()->update([
                'password' => bcrypt($data['password'])
            ]);
    }

    public function delete(Coach $coach)
    {
        $coach->delete();
        $coach->user->roles()->detach();
        $coach->user()->delete();
        return $coach;
    }
}
