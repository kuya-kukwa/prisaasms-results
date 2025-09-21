<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
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

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    // Helper methods for role-based functionality
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
