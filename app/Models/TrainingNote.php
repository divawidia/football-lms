<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class TrainingNote extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'note',
        'trainingId',
        'teamId',
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class, 'trainingId');
    }
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teamId');
    }
}
