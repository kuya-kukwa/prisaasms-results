<?php

namespace App\Models\Layer3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer3\GameMatch;
use App\Models\Layer3\ResultMetric;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
    use HasFactory, SoftDeletes;

    const OUTCOMES = ['win', 'loss', 'draw'];

    protected $fillable = ['match_id', 'team_id', 'athlete_id', 'score', 'outcome'];

    public function match() { return $this->belongsTo(GameMatch::class); }
    public function metrics() { return $this->hasMany(ResultMetric::class); }

    // Validation: either team_id or athlete_id required
    public function isValid() {
        return $this->team_id || $this->athlete_id;
    }
}
