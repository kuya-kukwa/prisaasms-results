<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameMatch extends Model
{
    protected $table = 'game_matches';

    protected $fillable = [
        'match_code',
        'title',
        'description',
        'sport_id',
        'venue_id',
        'schedule_id',
        'tournament_id',
        'created_by',
        'team_a_id',
        'team_b_id',
        'participants',
        'scheduled_start',
        'actual_start',
        'actual_end',
        'duration_minutes',
        'halftime_duration',
        'overtime_minutes',
        'match_type',
        'status',
        'round',
        'match_number',
        'officials_assigned',
        'head_referee_id',
        'score_team_a',
        'score_team_b',
        'final_score_team_a',
        'final_score_team_b',
        'winner_id',
        'result_type',
        'is_upset',
        'match_statistics',
        'penalties',
        'timeouts_used',
        'match_notes',
        'result_confirmed',
        'result_confirmed_at',
        'result_confirmed_by',
        'protest_filed',
        'protest_details'
    ];

    protected $casts = [
        'participants' => 'array',
        'scheduled_start' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'halftime_duration' => 'datetime',
        'duration_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'match_number' => 'integer',
        'officials_assigned' => 'array',
        'score_team_a' => 'array',
        'score_team_b' => 'array',
        'final_score_team_a' => 'integer',
        'final_score_team_b' => 'integer',
        'is_upset' => 'boolean',
        'match_statistics' => 'array',
        'penalties' => 'array',
        'timeouts_used' => 'array',
        'result_confirmed' => 'boolean',
        'result_confirmed_at' => 'datetime',
        'protest_filed' => 'boolean'
    ];

    // Relationships
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function teamA(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function headReferee(): BelongsTo
    {
        return $this->belongsTo(Official::class, 'head_referee_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'result_confirmed_by');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class, 'match_id');
    }

    // Accessors
    public function getScoreDifferenceAttribute(): int
    {
        if ($this->final_score_team_a !== null && $this->final_score_team_b !== null) {
            return abs($this->final_score_team_a - $this->final_score_team_b);
        }
        return 0;
    }

    public function getIsDrawAttribute(): bool
    {
        return $this->final_score_team_a === $this->final_score_team_b && $this->status === 'completed';
    }
}
