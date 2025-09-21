<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'team_code',
        'school_id',
        'sport_id',
        'coach_id',
        'gender_category',
        'division',
        'season_year',
        'team_logo',
        'wins',
        'losses',
        'draws',
        'win_percentage',
        'status',
        'contact_person',
    ];

    protected $casts = [
        'season_year' => 'integer',
        'wins' => 'integer',
        'losses' => 'integer',
        'draws' => 'integer',
        'win_percentage' => 'decimal:2'
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'team_a_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'team_b_id');
    }

    public function wonMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'winner_id');
    }

    // Accessors
    public function getTotalMatchesAttribute(): int
    {
        return $this->wins + $this->losses + $this->draws;
    }

    public function getWinPercentageAttribute(): float
    {
        $total = $this->getTotalMatchesAttribute();
        return $total > 0 ? round(($this->wins / $total) * 100, 2) : 0;
    }
}
