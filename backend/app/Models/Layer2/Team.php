<?php

namespace App\Models\Layer2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer1\School;
use App\Models\Layer1\Division;
use App\Models\Layer2\Sport;
use App\Models\Layer2\Athlete;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'team_code',
        'school_id',
        'sport_id',
        'coach_id',
        'division_id',
        'season_year',
        'team_logo',
        'status'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function athletes()
    {
        return $this->belongsToMany(Athlete::class, 'athlete_team');
    }
}
