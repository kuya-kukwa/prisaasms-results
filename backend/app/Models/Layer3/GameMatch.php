<?php

namespace App\Models\Layer3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer3\Schedule;
use App\Models\Layer2\Team;
use App\Models\Layer3\Result;
use App\Models\Layer3\MatchOfficial;
use Illuminate\Database\Eloquent\SoftDeletes;


class GameMatch extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS = ['pending', 'ongoing', 'completed', 'cancelled'];

    protected $fillable = [
        'schedule_id', 'team_a_id', 'team_b_id', 'winner_id', 'status', 'scheduled_at'
    ];

    protected $casts = ['scheduled_at' => 'datetime'];

    public function schedule() { return $this->belongsTo(Schedule::class); }
    public function teamA() { return $this->belongsTo(Team::class, 'team_a_id'); }
    public function teamB() { return $this->belongsTo(Team::class, 'team_b_id'); }
    public function winner() { return $this->belongsTo(Team::class, 'winner_id'); }
    public function results() { return $this->hasMany(Result::class); }
    public function officials() { return $this->hasMany(MatchOfficial::class, 'match_id'); }
}
