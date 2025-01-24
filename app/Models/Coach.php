<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class Coach extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'certificationId',
        'specializationId',
        'height',
        'weight',
        'joinDate',
        'hireDate',
        'userId'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'coach_team', 'coachId', 'teamId')->withTimestamps();
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class, 'coach_training_attendance', 'coachId', 'trainingId')
            ->withPivot(
                'attendanceStatus',
                'note',
                'teamId'
            )->withTimestamps();
    }

    public function matches(): BelongsToMany
    {
        return $this->belongsToMany(MatchModel::class, 'coach_match_attendance', 'coachId', 'matchId')
            ->withPivot(
                'attendanceStatus',
                'note',
                'teamId'
            )->withTimestamps();
    }
    public function coachMatchStats(): BelongsToMany
    {
        return $this->belongsToMany(MatchModel::class, 'coach_match_stats', 'coachId', 'matchId')
            ->withPivot(
                'teamId',
                'teamScore',
                'teamOwnGoal',
                'teamPossesion',
                'teamShotOnTarget',
                'teamShots',
                'teamTouches',
                'teamTackles',
                'teamClearances',
                'teamCorners',
                'teamOffsides',
                'teamYellowCards',
                'teamRedCards',
                'teamFoulsConceded',
                'resultStatus',
                'teamPasses',
                'goalConceded',
                'goalScored',
                'cleanSheets',
            )->withTimestamps();
    }

    public function playerSkillStats(): HasMany
    {
        return $this->hasMany(PlayerSkillStats::class, 'coachId');
    }
    public function playerPerformanceReviews(): HasMany
    {
        return $this->hasMany(PlayerPerformanceReview::class, 'coachId');
    }

    public function certification(): BelongsTo
    {
        return $this->belongsTo(CoachCertification::class, 'certificationId');
    }
    public function specialization(): BelongsTo
    {
        return $this->belongsTo(CoachSpecialization::class, 'specializationId');
    }

}
