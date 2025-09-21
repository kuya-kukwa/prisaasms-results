<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OverallChampion extends Model
{
    protected $fillable = [
        'prisaa_year_id',
        'level',
        'category',
        'school_id',
        'points',
        'gold_medals',
        'silver_medals',
        'bronze_medals',
        'total_medals',
        'rank',
        'region',
        'province'
    ];

    protected $casts = [
        'prisaa_year_id' => 'integer',
        'school_id' => 'integer',
        'points' => 'decimal:2',
        'gold_medals' => 'integer',
        'silver_medals' => 'integer',
        'bronze_medals' => 'integer',
        'total_medals' => 'integer',
        'rank' => 'integer'
    ];

    // Relationships
    public function prisaaYear(): BelongsTo
    {
        return $this->belongsTo(PrisaaYear::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    // Accessors
    public function getMedalBreakdownAttribute(): array
    {
        return [
            'gold' => $this->gold_medals,
            'silver' => $this->silver_medals,
            'bronze' => $this->bronze_medals,
            'total' => $this->total_medals
        ];
    }

    public function getChampionshipTitleAttribute(): string
    {
        $titles = [
            1 => 'Overall Champion',
            2 => '1st Runner-up',
            3 => '2nd Runner-up'
        ];

        return $titles[$this->rank] ?? ($this->rank . 'th Place');
    }
}
