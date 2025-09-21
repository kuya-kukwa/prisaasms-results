<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sport extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'gender_category',
        'max_players_per_team',
        'min_players_per_team',
        'scoring_system',
        'game_duration_minutes',
        'tournament_format',
        'has_ranking_system',
        'status',
        'icon'
    ];

    protected $casts = [
        'scoring_system' => 'array',
        'max_players_per_team' => 'integer',
        'min_players_per_team' => 'integer',
        'game_duration_minutes' => 'integer',
        'has_ranking_system' => 'boolean'
    ];

    // Relationships
    public function athletes(): HasMany
    {
        return $this->hasMany(Athlete::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function gameMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(Ranking::class);
    }

    public function medalTallies(): HasMany
    {
        return $this->hasMany(MedalTally::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    // Helper methods for PRISAA sports
    public function isTeamSport(): bool
    {
        return $this->category === 'team_sport';
    }

    public function isIndividualSport(): bool
    {
        return $this->category === 'individual_sport';
    }

    public function getActiveTeamsCount(): int
    {
        return $this->teams()->where('status', 'active')->count();
    }

    public function getActiveAthletesCount(): int
    {
        return $this->athletes()->where('status', 'active')->count();
    }

    public function getUpcomingMatches()
    {
        return $this->gameMatches()
            ->where('match_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('match_date')
            ->get();
    }
}
