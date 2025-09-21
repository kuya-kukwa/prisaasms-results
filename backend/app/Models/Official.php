<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Official extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'birthdate',
        'contact_number',
        'email',
        'avatar',
        'certification_level',
        'official_type',
        'sports_certified',
        'years_experience',
        'status',
        'available_for_assignment',
        'availability_schedule'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'sports_certified' => 'array',
        'years_experience' => 'integer',
        'available_for_assignment' => 'boolean',
        'availability_schedule' => 'array'
    ];

    // Relationships
    public function gameMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'head_referee_id');
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
