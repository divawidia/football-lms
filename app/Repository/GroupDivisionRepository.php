<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\Competition;
use App\Models\Match;
use App\Models\GroupDivision;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;

class GroupDivisionRepository
{
    protected GroupDivision $groupDivision;
    public function __construct(GroupDivision $groupDivision)
    {
        $this->groupDivision = $groupDivision;
    }

    public function getAll()
    {
        return $this->groupDivision->get();
    }

    public function find($id)
    {
        return $this->groupDivision->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->groupDivision->create($data);
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
