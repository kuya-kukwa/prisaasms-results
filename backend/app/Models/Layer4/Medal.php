<?php

namespace App\Models\Layer4;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer3\Result;

class Medal extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'team_id',
        'tournament_id',
        'medal_type',
    ];

    public function athlete()
    {
        return $this->belongsTo(\App\Models\Layer2\Athlete::class);
    }

    public function team()
    {
        return $this->belongsTo(\App\Models\Layer2\Team::class);
    }

    public function tournament()
    {
        return $this->belongsTo(\App\Models\Layer1\Tournament::class);
    }
}
