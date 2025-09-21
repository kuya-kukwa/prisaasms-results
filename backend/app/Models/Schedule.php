<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'sport_id',
        'venue_id',
        'tournament_id',
        'tournament_manager_id',
        'created_by',
        'event_type',
        'status',
        'priority',
        'participants',
        'officials_assigned',
        // PRISAA-specific fields
        'competition_level', // elementary, high_school, college
        'age_group', // u12, u14, u16, u18, u21, open, masters
        'gender_category', // mens, womens, mixed, co_ed
        'educational_level', // elementary, middle_school, high_school, college, professional
        'sport_category', // weight class, division, specialty
        'round_type', // qualifying, preliminary, semi_final, final, bronze, gold
        'heat_number', // for track events
        'lane_number', // for track events
        'court_field_number', // for team sports
        'is_team_event', // boolean
        'max_teams_per_school', // PRISAA rule
        'qualification_criteria', // json
        'weather_conditions', // for outdoor events
        'technical_officials_required', // number
        'medical_officials_required', // number
        'spectator_capacity',
        'broadcast_info',
        'live_stream_url',
        'result_format', // individual, team, relay
        'scoring_system_used',
        'protest_deadline_hours',
        'appeal_process_info'
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer',
        'participants' => 'array',
        'officials_assigned' => 'array',
        // PRISAA-specific casts
        'is_team_event' => 'boolean',
        'max_teams_per_school' => 'integer',
        'qualification_criteria' => 'array',
        'technical_officials_required' => 'integer',
        'medical_officials_required' => 'integer',
        'spectator_capacity' => 'integer',
        'protest_deadline_hours' => 'integer',
        'heat_number' => 'integer',
        'lane_number' => 'integer',
        'court_field_number' => 'integer'
    ];

    // Relationships
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function tournamentManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tournament_manager_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function gameMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }

    // PRISAA-specific relationships
    public function participatingSchools()
    {
        return $this->belongsToMany(School::class, 'schedule_school', 'schedule_id', 'school_id')
                    ->withPivot('registration_status', 'team_count', 'qualification_status')
                    ->withTimestamps();
    }

    public function assignedOfficials()
    {
        return $this->belongsToMany(Official::class, 'schedule_official', 'schedule_id', 'official_id')
                    ->withPivot('role', 'assignment_time', 'confirmation_status')
                    ->withTimestamps();
    }

    public function participatingTeams()
    {
        return $this->belongsToMany(Team::class, 'schedule_team', 'schedule_id', 'team_id')
                    ->withPivot('seed_number', 'qualification_round', 'final_position')
                    ->withTimestamps();
    }

    public function participatingAthletes()
    {
        return $this->belongsToMany(Athlete::class, 'schedule_athlete', 'schedule_id', 'athlete_id')
                    ->withPivot('bib_number', 'heat_number', 'lane_number', 'performance_result')
                    ->withTimestamps();
    }

    // Accessors
    public function getDurationHoursAttribute(): float
    {
        return $this->duration_minutes ? round($this->duration_minutes / 60, 2) : 0;
    }

    /**
     * Get the assigned tournament manager for this schedule.
     * Returns direct tournament manager if set, otherwise falls back to tournament's manager.
     */
    public function getAssignedManagerAttribute(): ?User
    {
        // First check if there's a directly assigned tournament manager
        if ($this->tournament_manager_id && $this->tournamentManager) {
            return $this->tournamentManager;
        }
        
        // Fall back to the tournament's manager if available
        if ($this->tournament && $this->tournament->tournament_manager_id) {
            return $this->tournament->tournamentManager;
        }
        
        return null;
    }
}
