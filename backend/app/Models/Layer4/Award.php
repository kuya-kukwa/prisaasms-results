<?php

namespace App\Models\Layer4;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer2\Athlete;
use App\Models\Layer2\Team;

class Award extends Model
{
    use HasFactory;

    protected $fillable = ['athlete_id', 'team_id', 'type', 'description'];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
