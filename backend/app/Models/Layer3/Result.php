<?php

namespace App\Models\Layer3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer3\Match;
use App\Models\Layer3\ResultMetric;

class Result extends Model
{
    use HasFactory;

    protected $fillable = ['match_id', 'team_id', 'athlete_id', 'score', 'rank'];

    public function match()
    {
        return $this->belongsTo(Match::class);
    }

    public function metrics()
    {
        return $this->hasMany(ResultMetric::class);
    }
}
