<?php

namespace App\Repository\Interface;

use App\Models\Coach;
use App\Models\Competition;
use App\Models\GroupDivision;
use App\Models\Player;

interface TeamRepositoryInterface
{
    public function getAll();

    public function getByTeamside($teamSide, $exceptTeamId = null);

    public function getInArray(array $ids);

    public function getTeamsHaventJoinedByCoach(Coach $coach);

    public function getTeamsHaventJoinedByPLayer(Player $player);

    public function getTeamsHaventJoinedCompetition(Competition $competition, $teamSide);

    public function getTeamsJoinedGroupDivision(GroupDivision $groupDivision, $teamSide, $exceptTeamId = null);

    public function getJoinedCompetition(Competition $competition);

    public function find($id);

    public function whereId($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}
