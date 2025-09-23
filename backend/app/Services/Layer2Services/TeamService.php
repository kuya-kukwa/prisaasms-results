<?php

namespace App\Services;

use App\Models\Layer2\Team;
use App\Models\Layer2\Athlete;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TeamService
{
    /**
     * List teams with filters, sorting and pagination.
     *
     * $filters keys: name, school_id, sport_id, division_id, status, search
     */
    public function list(array $filters = [], int $perPage = 15, string $sortBy = 'created_at', string $sortDir = 'desc'): LengthAwarePaginator
    {
        $query = Team::with(['school', 'sport', 'division', 'athletes']);

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (!empty($filters['sport_id'])) {
            $query->where('sport_id', $filters['sport_id']);
        }

        if (!empty($filters['division_id'])) {
            $query->where('division_id', $filters['division_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('short_name', 'like', "%{$s}%")
                  ->orWhere('team_code', 'like', "%{$s}%");
            });
        }

        $allowedSorts = ['id','name','team_code','created_at','updated_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Find single team
     */
    public function find(int $id): Team
    {
        return Team::with(['school', 'sport', 'division', 'athletes'])->findOrFail($id);
    }

    /**
     * Create team (returns instance)
     */
    public function create(array $data): Team
    {
        return DB::transaction(function () use ($data) {
            // You may generate team_code here if not provided
            if (empty($data['team_code'])) {
                $data['team_code'] = $this->generateTeamCode($data['school_id'] ?? null);
            }

            $team = Team::create($data);
            return $team->load(['school', 'sport', 'division', 'athletes']);
        });
    }

    /**
     * Update team
     */
    public function update(Team $team, array $data): Team
    {
        return DB::transaction(function () use ($team, $data) {
            $team->update($data);
            return $team->fresh()->load(['school', 'sport', 'division', 'athletes']);
        });
    }

    /**
     * Soft delete
     */
    public function delete(Team $team): bool
    {
        return DB::transaction(function () use ($team) {
            return (bool) $team->delete();
        });
    }

    /**
     * Restore soft-deleted
     */
    public function restore(int $id): ?Team
    {
        $team = Team::withTrashed()->findOrFail($id);
        if ($team->trashed()) {
            $team->restore();
        }
        return $team->load(['school', 'sport', 'division', 'athletes']);
    }

    /**
     * Permanently delete
     */
    public function forceDelete(int $id): bool
    {
        $team = Team::withTrashed()->findOrFail($id);
        return DB::transaction(function () use ($team) {
            return (bool) $team->forceDelete();
        });
    }

    /**
     * List athletes in team
     */
    public function listAthletes(Team $team)
    {
        return $team->athletes()->with(['school','division'])->get();
    }

    /**
     * Attach single athlete
     */
    public function attachAthlete(Team $team, Athlete $athlete): void
    {
        DB::transaction(function () use ($team, $athlete) {
            $team->athletes()->syncWithoutDetaching([$athlete->id]);
        });
    }

    /**
     * Detach single athlete
     */
    public function detachAthlete(Team $team, Athlete $athlete): void
    {
        DB::transaction(function () use ($team, $athlete) {
            $team->athletes()->detach($athlete->id);
        });
    }

    /**
     * Sync athletes (replace)
     * $athleteIds = [1,2,3]
     */
    public function syncAthletes(Team $team, array $athleteIds): void
    {
        DB::transaction(function () use ($team, $athleteIds) {
            $team->athletes()->sync($athleteIds);
        });
    }

    /**
     * Helper to generate a simple team code.
     * You can change to any logic you prefer.
     */
    protected function generateTeamCode(?int $schoolId = null): string
    {
        $prefix = $schoolId ? 'S' . $schoolId : 'T';
        $rand = strtoupper(substr(uniqid(), -6));
        return "{$prefix}-{$rand}";
    }
}
