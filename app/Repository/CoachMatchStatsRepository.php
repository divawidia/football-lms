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
        } elseif ($retrievalMethod == 'sum') {
            return $query->sum($column);
        } else {
            return $query->get($column);
        }
    }
    public function totalMatchPlayed(Coach $coach)
    {
        return $this->coachMatchStats($coach)->count();
    }
    public function thisMonthTotalMatchPlayed(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->count();
    }

    public function totalGoals(Coach $coach)
    {
        return $this->coachMatchStats($coach)->sum('teamScore');
    }
    public function thisMonthTotalGoals(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->sum('teamScore');
    }

    public function totalGoalsConceded(Coach $coach)
    {
        return $this->coachMatchStats($coach)->sum('goalConceded');
    }
    public function thisMonthTotalGoalsConceded(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->sum('goalConceded');
    }

    public function totalCleanSheets(Coach $coach)
    {
        return $this->coachMatchStats($coach)->sum('cleanSheets');
    }
    public function thisMonthTotalCleanSheets(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->sum('cleanSheets');
    }

    public function totalOwnGoals(Coach $coach)
    {
        return $this->coachMatchStats($coach)->sum('teamOwnGoal');
    }
    public function thisMonthTotalOwnGoals(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->sum('teamOwnGoal');
    }

    public function totalWins(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->where('resultStatus', 'Win')
            ->count();
    }
    public function thisMonthTotalWins(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->where('resultStatus', 'Win')
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->count();
    }

    public function totalLosses(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->where('resultStatus', 'Lose')
            ->count();
    }
    public function thisMonthTotalLosses(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->where('resultStatus', 'Lose')
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->count();
    }

    public function totalDraws(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->where('resultStatus', 'Draw')
            ->count();
    }
    public function thisMonthTotalDraws(Coach $coach)
    {
        return $this->coachMatchStats($coach)
            ->where('resultStatus', 'Draw')
            ->whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()])
            ->count();
    }
}
