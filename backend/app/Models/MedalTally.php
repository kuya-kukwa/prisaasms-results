<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedalTally extends Model
{
    protected $table = 'medal_tallies';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'entity_name',
        'tournament_id',
        'sport_id',
        'prisaa_year_id',
        'season',
        'division',
        'category',
        'gold_medals',
        'silver_medals',
        'bronze_medals',
        'total_medals',
        'gold_points',
        'silver_points',
        'bronze_points',
        'total_points',
        'rank',
        'previous_rank',
        'medal_breakdown',
        'event_results',
        'tally_date',
        'period_start',
        'period_end',
        'is_current',
        'is_final'
    ];

    protected $casts = [
        'gold_medals' => 'integer',
        'silver_medals' => 'integer',
        'bronze_medals' => 'integer',
        'total_medals' => 'integer',
        'gold_points' => 'decimal:2',
        'silver_points' => 'decimal:2',
        'bronze_points' => 'decimal:2',
        'total_points' => 'decimal:2',
        'rank' => 'integer',
        'previous_rank' => 'integer',
        'medal_breakdown' => 'array',
        'event_results' => 'array',
        'tally_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'is_current' => 'boolean',
        'is_final' => 'boolean'
    ];

    // Relationships
    public function prisaaYear(): BelongsTo
    {
        return $this->belongsTo(PrisaaYear::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    // Polymorphic relationship for entity (could be team, school, athlete)
    public function entity()
    {
        return $this->morphTo();
    }

    // Accessors
    public function getTotalMedalsAttribute(): int
    {
        return $this->gold_medals + $this->silver_medals + $this->bronze_medals;
    }

    public function getTotalPointsAttribute(): float
    {
        return $this->gold_points + $this->silver_points + $this->bronze_points;
    }

    public function getRankChangeAttribute(): int
    {
        if ($this->previous_rank && $this->rank) {
            return $this->previous_rank - $this->rank;
        }
        return 0;
    }
}
