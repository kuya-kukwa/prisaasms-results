<?php

namespace App\Models\Layer2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer1\School;
use App\Models\Layer1\Division;
use App\Models\Layer2\Sport;
use App\Models\Layer2\Team;
use Illuminate\Database\Eloquent\SoftDeletes;
class Athlete extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    'first_name',
    'last_name',
    'gender',
    'birthdate',
    'school_id',
    'weight_class_id',
];

    protected $dates = ['birthdate'];
   

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function sports()
    {
        return $this->belongsToMany(Sport::class, 'athlete_sport');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'athlete_team');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getAgeAttribute(): int
    {
        return $this->birthdate ? $this->birthdate->age : 0;
    }
    
}
