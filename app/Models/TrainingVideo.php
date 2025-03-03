<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class TrainingVideo extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'userId',
        'trainingTitle',
        'description',
        'previewPhoto',
        'totalLesson',
        'totalMinute',
        'level',
        'status',
    ];
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_subscriptions', 'subscriptionId', 'invoiceId')
            ->withTimestamps();
    }
    public function players()
    {
        return $this->belongsToMany(Player::class, 'training_video_players', 'trainingVideoId', 'playerId')
            ->withPivot('progress', 'status', 'completed_at')->withTimestamps();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
    public function lessons()
    {
        return $this->hasMany(TrainingVideoLesson::class, 'trainingVideoId', 'id');
    }
}
