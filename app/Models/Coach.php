<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coach extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'firstName',
        'lastName',
        'certificationLevel',
        'specialization',
        'height',
        'weight',
        'joinDate',
        'status',
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

}
