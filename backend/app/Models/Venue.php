<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'venue_code',
        'address',
        'region_id',
        'host_school_id',
        'contact_person',
        'contact_number',
        'email',
        'venue_type',
        'venue_category',
        'status'
    ];

    // Relationships
    public function hostSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'host_school_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function gameMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }
}
