<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'address',
        'region_id',
        'logo',
        'status'
    ];

    protected $casts = [
        // No specific casts needed for basic school model
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

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class, 'host_school_id');
    }

    public function hostedTournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'host_school_id');
    }

    public function wonTournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'champion_school_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
