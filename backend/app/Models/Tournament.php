<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tournament extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'tournament_code',
        'description',
        'type',
        'level',
        'scope',
        'start_date',
        'end_date',
        'registration_end',
        'host_location',
        'host_school_id',
        'host_region_id',
        'tournament_manager_id',
        'prisaa_year_id',
        'has_medal_tally',
        'sports_included',
        'status',
        'is_public',
        'champion_school_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_end' => 'date',
        'sports_included' => 'array',
        'has_medal_tally' => 'boolean',
        'is_public' => 'boolean'
    ];

    // Relationships
    public function prisaaYear(): BelongsTo
    {
        return $this->belongsTo(PrisaaYear::class);
    }

    public function hostSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'host_school_id');
    }

    public function hostRegion(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'host_region_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tournament_manager_id');
    }

    public function championSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'champion_school_id');
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

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'tournament_regions');
    }
}
