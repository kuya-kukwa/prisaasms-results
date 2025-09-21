<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    protected $fillable = [
        'match_id',
        'tournament_id',
        'sport_id',
        'event_name',
        'participant_type',
        'participant_id',
        'participant_name',
        'school_id',
        'position',
        'medal_type',
        'score',
        'round_type',
        'category',
        'competition_date',
        'verified',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'position' => 'integer',
        'competition_date' => 'date',
        'verified' => 'boolean',
        'verified_at' => 'datetime'
    ];

    // Relationships
    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Polymorphic relationship for participant (could be team or athlete)
    public function participant()
    {
        return $this->morphTo();
    }

    // Accessors
    public function getIsMedalWinnerAttribute(): bool
    {
        return in_array($this->medal_type, ['gold', 'silver', 'bronze']);
    }

    public function getMedalPointsAttribute(): float
    {
        return match($this->medal_type) {
            'gold' => 3.0,
            'silver' => 2.0,
            'bronze' => 1.0,
            default => 0.0
        };
    }
}
