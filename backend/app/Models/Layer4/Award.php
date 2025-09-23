<?php

namespace App\Models\Layer4;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer2\Athlete;
use App\Models\Layer2\Team;

class Award extends Model
{
    use HasFactory;

    protected $table = 'player_awards';

    protected $fillable = [
        'athlete_id',
        'tournament_id',
        'match_id',
        'award_type',
        'remarks',
    ];

    public function athlete()
    {
        return $this->belongsTo(\App\Models\Layer2\Athlete::class);
    }

    public function tournament()
    {
        return $this->belongsTo(\App\Models\Layer1\Tournament::class);
    }

    public function match()
    {
        return $this->belongsTo(\App\Models\Layer3\GameMatch::class);
    }
}
