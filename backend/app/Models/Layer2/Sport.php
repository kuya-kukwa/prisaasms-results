<?php

namespace App\Models\Layer2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Layer1\User;
use App\Models\Layer2\SportSubcategory;
use App\Models\Layer2\WeightClass;

class Sport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'result_format',
        'description',
    ];

    // -------------------------------
    // Relationships
    // -------------------------------
    public function subcategories(): HasMany
    {
        return $this->hasMany(SportSubcategory::class);
    }

    public function weightClasses(): HasMany
    {
        return $this->hasMany(WeightClass::class);
    }

    /**
     * Officials assigned to this sport (via pivot table officials_sport).
     */
    public function officials(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'officials_sport',  // pivot table
            'sport_id',         // FK to sports table
            'official_id'       // FK to users table
        )->withTimestamps()->withPivot('deleted_at');
    }
}
