<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Athlete extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'gender',
        'birthdate',
        'avatar',
        'school_id',
        'sport_id',
        'athlete_number',
        'status'
    ];

    protected $casts = [
        'birthdate' => 'date'
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

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'coach_id');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getAgeAttribute(): int
    {
        return $this->birthdate ? $this->birthdate->age : 0;
    }
}
