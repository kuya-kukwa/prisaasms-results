<?php

namespace App\Models\Layer1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Import related models
use App\Models\Layer1\School;
use App\Models\Layer1\Tournament;
use App\Models\Layer2\Sport;
use App\Models\Layer3\Schedule;
use App\Models\Layer3\GameMatch;
use App\Models\Layer3\Result;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;
    protected $guard_name = 'web'; // 🔹 important for Spatie roles & permissions


    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'contact_number',
        'role',
        'avatar',
        'school_id',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // -------------------------------
    // Relationships
    // -------------------------------

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class)->withDefault();
    }

    public function managedTournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'tournament_manager_id');
    }

    public function createdSchedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'created_by');
    }

    public function createdMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'created_by');
    }

    public function confirmedResults(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'result_confirmed_by');
    }

    public function verifiedResults(): HasMany
    {
        return $this->hasMany(Result::class, 'verified_by');
    }

    /**
     * Sports assigned to this official (via pivot table officials_sport).
     */
    public function sports(): BelongsToMany
    {
        return $this->belongsToMany(
            Sport::class,
            'officials_sport',  // pivot table
            'official_id',      // FK to users table
            'sport_id'          // FK to sports table
        )->withTimestamps();
    }

    // -------------------------------
    // Accessors
    // -------------------------------

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    // -------------------------------
    // Role Helpers
    // -------------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCoach(): bool
    {
        return $this->role === 'coach';
    }

    public function isTournamentManager(): bool
    {
        return $this->role === 'tournament_manager';
    }

    public function hasSchoolAffiliation(): bool
    {
        return !is_null($this->school_id);
    }

    public function isIndependentTournamentManager(): bool
    {
        return $this->isTournamentManager() && !$this->hasSchoolAffiliation();
    }

    public function getAffiliationAttribute(): string
    {
        if ($this->hasSchoolAffiliation()) {
            return $this->school->name ?? 'Unknown School';
        }

        if ($this->isTournamentManager()) {
            return 'Independent Tournament Manager';
        }

        return 'No Affiliation';
    }
}
