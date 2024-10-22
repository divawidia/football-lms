<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;

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
        $coach->teams()->attach($data['team']);
        return $coach;
    }

    public function update($id, array $data)
    {
        $coach = $this->find($id);
        $coach->update($data);
        return $coach;
    }

    public function delete($id)
    {
        $coach = $this->find($id);
        $coach->delete();
        return $coach;
    }
}
