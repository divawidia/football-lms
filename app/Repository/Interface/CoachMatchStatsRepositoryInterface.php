<?php

namespace App\Repository\Interface;

use App\Models\Coach;

interface CoachMatchStatsRepositoryInterface
{
    /**
     * Retrieve the match statistics for a specific coach.
     *
     * @param Coach $coach
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function coachMatchStats(Coach $coach);

    /**
     * Get the total number of matches played by a coach.
     *
     * @param Coach $coach
     * @return int
     */
    public function totalMatchPlayed(Coach $coach);

    /**
     * Get the total number of matches played by a coach this month.
     *
     * @param Coach $coach
     * @return int
     */
    public function thisMonthTotalMatchPlayed(Coach $coach);

    /**
     * Get the total goals scored by a coach's team.
     *
     * @param Coach $coach
     * @return int
     */
    public function totalGoals(Coach $coach);

    /**
     * Get the total goals scored by a coach's team this month.
     *
     * @param Coach $coach
     * @return int
     */
    public function thisMonthTotalGoals(Coach $coach);

    /**
     * Get the total goals conceded by a coach's team.
     *
     * @param Coach $coach
     * @return int
     */
    public function totalGoalsConceded(Coach $coach);

    /**
     * Get the total goals conceded by a coach's team this month.
     *
     * @param Coach $coach
     * @return int
     */
    public function thisMonthTotalGoalsConceded(Coach $coach);

    /**
     * Get the total clean sheets achieved by a coach's team.
     *
     * @param Coach $coach
     * @return int
     */
    public function totalCleanSheets(Coach $coach);

    /**
     * Get the total clean sheets achieved by a coach's team this month.
     *
     * @param Coach $coach
     * @return int
     */
    public function thisMonthTotalCleanSheets(Coach $coach);

    /**
     * Get the total own goals by a coach's team.
     *
     * @param Coach $coach
     * @return int
     */
    public function totalOwnGoals(Coach $coach);

    /**
     * Get the total own goals by a coach's team this month.
     *
     * @param Coach $coach
     * @return int
     */
    public function thisMonthTotalOwnGoals(Coach $coach);

    /**
     * Get the total wins achieved by a coach's team.
     *
     * @param Coach $coach
     * @return int
     */
    public function totalWins(Coach $coach);

    /**
     * Get the total wins achieved by a coach's team this month.
     *
     * @param Coach $coach
     * @return int
     */
    public function thisMonthTotalWins(Coach $coach);

    /**
     * Get the total losses by a coach's team.
     *
     * @param Coach $coach
     * @return int
     */
    public function totalLosses(Coach $coach);

    /**
     * Get the total losses by a coach's team this month.
     *
     * @param Coach $coach
     * @return int
     */
    public function thisMonthTotalLosses(Coach $coach);

    /**
     * Get the total draws by a coach's team.
     *
     * @param Coach $coach
     * @return int
     */
    public function totalDraws(Coach $coach);

    /**
     * Get the total draws by a coach's team this month.
     *
     * @param Coach $coach
     * @return int
     */
    public function thisMonthTotalDraws(Coach $coach);
}
