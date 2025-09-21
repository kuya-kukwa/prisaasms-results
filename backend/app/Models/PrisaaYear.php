<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrisaaYear extends Model
{
    protected $fillable = [
        'year',
        'host_region',
        'host_province',
        'host_city',
        'start_date',
        'end_date',
        'total_participants',
        'total_schools',
        'total_sports',
        'total_events',
        'status',
        'director_id',
        'description',
        'highlights',
        'achievements',
        'records_broken'
    ];

    protected $casts = [
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_participants' => 'integer',
        'total_schools' => 'integer',
        'total_sports' => 'integer',
        'total_events' => 'integer',
        'highlights' => 'array',
        'achievements' => 'array',
        'records_broken' => 'array'
    ];

    // Relationships
    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'prisaa_year_id');
    }

    public function provincialGames(): HasMany
    {
        return $this->tournaments()->where('level', 'provincial');
    }

    public function regionalGames(): HasMany
    {
        return $this->tournaments()->where('level', 'regional');
    }

    public function nationalGames(): HasMany
    {
        return $this->tournaments()->where('level', 'national');
    }

    public function medalTallies(): HasMany
    {
        return $this->hasMany(MedalTally::class, 'prisaa_year_id');
    }

    public function overallChampions(): HasMany
    {
        return $this->hasMany(OverallChampion::class, 'prisaa_year_id');
    }

    // Accessors and Methods
    public function getDurationDaysAttribute(): int
    {
        return $this->start_date && $this->end_date 
            ? $this->start_date->diffInDays($this->end_date) + 1 
            : 0;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOngoing(): bool
    {
        return $this->status === 'ongoing';
    }

    public function getYearlyStatistics(): array
    {
        return [
            'year' => $this->year,
            'duration_days' => $this->duration_days,
            'total_participants' => $this->total_participants,
            'total_schools' => $this->total_schools,
            'total_sports' => $this->total_sports,
            'total_events' => $this->total_events,
            'provincial_games' => $this->provincialGames()->count(),
            'regional_games' => $this->regionalGames()->count(),
            'national_games' => $this->nationalGames()->count(),
            'total_tournaments' => $this->tournaments()->count(),
            'status' => $this->status
        ];
    }
}
