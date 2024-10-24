<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachMatchStat;
use App\Models\CoachSpecialization;
use App\Models\Team;
use Carbon\Carbon;

class CoachMatchStatsRepository
{
    protected Coach $coach;
    protected CoachMatchStat $coachMatchStat;

    public function __construct(Coach $coach, CoachMatchStat $coachMatchStat)
    {
        $this->coach = $coach;
        $this->coachMatchStat = $coachMatchStat;
    }

    public function coachMatchStats(Coach $coach)
    {
        return $this->coachMatchStat->where('coachId', $coach->id);
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
