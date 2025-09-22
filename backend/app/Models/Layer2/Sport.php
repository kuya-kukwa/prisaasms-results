<?php

namespace App\Models\Layer2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer2\SportSubcategory;
use App\Models\Layer2\Athlete;
use App\Models\Layer2\Team;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type']; // type: individual, team

    public function subcategories()
    {
        return $this->hasMany(SportSubcategory::class);
    }

    public function athletes()
    {
        return $this->belongsToMany(Athlete::class, 'athlete_sport');
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
