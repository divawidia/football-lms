<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class Player extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'positionId',
        'skill',
        'strongFoot',
        'height',
        'weight',
        'joinDate',
        'userId'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function position(): BelongsTo
    {
        return $this->belongsTo(PlayerPosition::class, 'positionId');
    }
    public function parents(): HasMany
    {
        return $this->hasMany(PlayerParrent::class, 'playerId');
    }
    public function playerMatchStats(): HasMany
    {
        return $this->hasMany(PlayerMatchStats::class, 'playerId');
    }
    public function playerSkillStats(): HasMany
    {
        return $this->hasMany(PlayerSkillStats::class, 'playerId');
    }
    public function playerPerformanceReview(): HasMany
    {
        return $this->hasMany(PlayerPerformanceReview::class, 'playerId');
    }
    public function matches(): BelongsToMany
    {
        return $this->belongsToMany(MatchModel::class, 'player_match_attendance', 'playerId', 'matchId')
            ->withPivot(
                'attendanceStatus',
                'note',
                'teamId'
            )->withTimestamps();
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class, 'player_training_attendance', 'playerId', 'trainingId')
            ->withPivot(
                'attendanceStatus',
                'note',
                'teamId'
            )->withTimestamps();
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'player_teams', 'playerId', 'teamId')->withTimestamps();
    }
    public function invoice(): HasMany
    {
        return $this->hasMany(Invoice::class, 'playerId', 'id');
    }
    public function trainingVideos(): BelongsToMany
    {
        return $this->belongsToMany(TrainingVideo::class, 'training_video_players', 'playerId', 'trainingVideoId')
            ->withPivot('progress', 'status', 'completed_at')
            ->withTimestamps();
    }

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(TrainingVideoLesson::class, 'player_lesson', 'playerId', 'lessonId')
            ->withPivot('completionStatus', 'completed_at')
            ->withTimestamps();
    }

    // Define the scope to filter players based on teams
    public function scopeWithTeams($query, $teams)
    {
        // Extract all team IDs from the $teams array
        if ($teams instanceof Collection) {
            $teamIds = collect($teams)->pluck('id')->all();
        } else {
            $teamIds[] = $teams;
        }

        return $query->whereHas('teams', function ($q) use ($teamIds) {
            $q->whereIn('teamId', $teamIds);
        });
    }
}
