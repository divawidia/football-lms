<?php

namespace App\Repository\Interface;

use App\Models\Admin;
use App\Models\Player;
use App\Models\Team;
use App\Models\TrainingVideo;
use App\Models\User;

interface PlayerRepositoryInterface
{

    public function getAll($teams = null, $position = null, $skill = null, $status = null);

    /**
     * Get players by array of player IDs
     *
     * @param array $playerIds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInArray($playerIds);

    /**
     * Get players not joined to a specific team
     *
     * @param Team $team
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlayerNotJoinSpecificTeam(Team $team);

    /**
     * Get players not assigned to a specific training video
     *
     * @param TrainingVideo $trainingVideo
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlayerNotAssignedTrainingCourse(TrainingVideo $trainingVideo);

    public function getMostAttended($startDate = null, $endDate = null, $teams = null, $relation = 'trainings');

    public function getMostDidntAttended($startDate = null, $endDate = null, $teams = null, $relation = 'trainings');
    /**
     * Find a player by ID
     *
     * @param int $id
     * @return Player
     */
    public function find($id);

    /**
     * Count player attendance
     *
     * @param Player $player
     * @param string $status
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function playerAttendanceCount(Player $player, $status = 'Attended', $startDate = null, $endDate = null);

    /**
     * Sum player match statistics
     *
     * @param Player $player
     * @param string $stats
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    public function playerMatchStatsSum(Player $player, $stats, $startDate = null, $endDate = null, Team $team = null);

    /**
     * Count matches played by a player
     *
     * @param Player $player
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function countMatchPlayed(Player $player, $startDate = null, $endDate = null, Team $team = null);

    /**
     * Count match results for a player
     *
     * @param Player $player
     * @param string $result
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function matchResults(Player $player, $result = null, $startDate = null, $endDate = null);

    /**
     * Create a new player
     *
     * @param array $data
     * @return Player
     */
    public function create(array $data);

    /**
     * Update an existing player
     *
     * @param Player $player
     * @param array $data
     * @return Player
     */
    public function update(Player $player, array $data);

    /**
     * Delete a player
     *
     * @param int $id
     * @return Player
     */
    public function delete($id);
}
