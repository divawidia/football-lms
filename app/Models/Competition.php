<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'logo',
        'startDate',
        'endDate',
        'location',
        'contactName',
        'contactPhone',
        'description',
        'status',
    ];

    public function groups(){
        return $this->hasMany(GroupDivision::class, 'competitionId');
    }

    public function matches(){
        return $this->hasMany(EventSchedule::class, 'competitionId');
    }
}
