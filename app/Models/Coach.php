<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coach extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'certificationLevel',
        'specialization',
        'height',
        'weight',
        'joinDate',
        'hireDate',
        'userId'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'coachId');
    }
    public function playerSkillStats()
    {
        return $this->hasMany(PlayerSkillStats::class, 'coachId');
    }
    public function playerPerformanceReview()
    {
        return $this->hasMany(PlayerPerformanceReview::class, 'coachId');
    }

    public function certification()
    {
        return $this->belongsTo(CoachCertification::class, 'certificationLevel');
    }
    public function specializations()
    {
        return $this->belongsTo(CoachSpecialization::class, 'specialization');
    }

}
