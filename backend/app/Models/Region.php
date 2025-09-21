<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Region extends Model
{
    protected $fillable = [
        'name',
        'code',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Relationships
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class, 'tournament_regions');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->name . ' (' . $this->code . ')';
    }
}
