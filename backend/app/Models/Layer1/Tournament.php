<?php

namespace App\Models\Layer1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer1\SeasonYear;
use App\Models\Layer1\School;
use App\Models\Layer3\Schedule;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'level', 'season_year_id'];

    public function seasonYear()
    {
        return $this->belongsTo(SeasonYear::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_tournament');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
