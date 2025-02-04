<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachMatchStats;
use App\Repository\Interface\CoachMatchStatsRepositoryInterface;
use Carbon\Carbon;

class CoachMatchStatsRepository implements CoachMatchStatsRepositoryInterface
{
    protected CoachMatchStats $coachMatchStat;

    public function __construct(CoachMatchStats $coachMatchStat)
    {
        $this->coachMatchStat = $coachMatchStat;
    }
    public function getAll(Coach $coach, $startDate = null, $endDate = null, $result = null, $matchPlayed = false, $retrievalMethod = 'count', $column = null)
    {
        $query = $this->coachMatchStat->where('coachId', $coach->id);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($result != null) {
            $query->where('resultStatus', $result);
        }

        if ($matchPlayed) {
            $query->whereRelation('match', 'status', 'Completed');
        }

        if ($retrievalMethod == 'count') {
            return $query->count();
        }
        elseif ($retrievalMethod == 'sum') {
            return $query->sum($column);
        }
        else {
            return $query->get($column);
        }
    }
}
