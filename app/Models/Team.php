<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teamName',
        'ageGroup',
        'division',
        'logo',
        'status',
        'academyId',
        'coachId'
    ];

    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coachId');
    }
    public function academy()
    {
        return $this->belongsTo(Academy::class, 'academyId');
    }
}
