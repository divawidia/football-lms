<?php

namespace App\Repository\Interface;

use App\Models\Coach;

interface CoachMatchStatsRepositoryInterface
{

    public function getAll(Coach $coach, $startDate = null, $endDate = null, $result = null, $matchPlayed = false, $retrievalMethod = 'count', $column = null);
}
