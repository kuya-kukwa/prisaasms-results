<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ranking extends Model
{
    protected $fillable = [
        'ranking_type',
        'entity_id',
        'entity_type',
        'sport_id',
        'tournament_id',
        'season',
        'division',
        'category',
        'current_rank',
        'previous_rank',
        'rank_change',
        'points',
        'rating',
        'matches_played',
        'wins',
        'losses',
        'draws',
        'win_percentage',
        'points_for',
        'points_against',
        'point_differential',
        'additional_stats',
        'ranking_date',
        'period_start',
        'period_end',
        'is_current',
        'is_final'
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'rating' => 'decimal:3',
        'win_percentage' => 'decimal:2',
        'point_differential' => 'decimal:2',
        'additional_stats' => 'array',
        'ranking_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'is_current' => 'boolean',
        'is_final' => 'boolean',
        'matches_played' => 'integer',
        'wins' => 'integer',
        'losses' => 'integer',
        'draws' => 'integer',
        'points_for' => 'integer',
        'points_against' => 'integer',
        'current_rank' => 'integer',
        'previous_rank' => 'integer',
        'rank_change' => 'integer'
    ];

    // Relationships
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    // Polymorphic relationship for entity (could be team, school, athlete)
    public function entity()
    {
        return $this->morphTo();
    }

    // Accessors
    public function getRankChangeDirectionAttribute(): string
    {
        if ($this->rank_change > 0) return 'up';
        if ($this->rank_change < 0) return 'down';
        return 'no_change';
    }

    public function getWinPercentageAttribute(): float
    {
        if ($this->matches_played > 0) {
            return round(($this->wins / $this->matches_played) * 100, 2);
        }
        return 0;
    }
}
