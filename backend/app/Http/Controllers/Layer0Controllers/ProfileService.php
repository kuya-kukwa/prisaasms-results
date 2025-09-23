<?php

namespace App\Models\Layer0;

use App\Models\Layer1\School;
use App\Models\Layer2\Athlete;
use App\Models\Layer1\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfileService
{
    /**
     * Retrieve profiles based on type, search, page, and limit.
     *
     * @param string $type
     * @param string|null $search
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getProfiles(string $type = 'all', ?string $search = null, int $page = 1, int $limit = 20): array
    {
        // Decide which profiles to fetch
        $profiles = match($type) {
            'school' => $this->getSchools($search),
            'athlete' => $this->getAthletes($search),
            'coach', 'official', 'tournament_manager' => $this->getUsersByRole($type, $search),
            default => $this->getAllProfiles($search),
        };

        return $this->paginateCollection($profiles, $page, $limit);
    }

    /**
     * Fetch all schools.
     */
    private function getSchools(?string $search)
    {
        $query = School::query();
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%");
        }

        return $query->get()->map(fn($school) => (object)[
            'id' => $school->id,
            'type' => 'school',
            'name' => $school->name,
            'short_name' => $school->short_name,
            'logo' => $school->logo,
            'status' => $school->status,
            'created_at' => $school->created_at,
            'updated_at' => $school->updated_at,
        ])->toArray();
    }

    /**
     * Fetch all athletes.
     */
    private function getAthletes(?string $search)
    {
        $query = Athlete::with('school');
        if ($search) {
            $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('athlete_number', 'like', "%{$search}%");
        }

        return $query->get()->map(fn($athlete) => (object)[
            'id' => $athlete->id,
            'type' => 'athlete',
            'first_name' => $athlete->first_name,
            'last_name' => $athlete->last_name,
            'athlete_number' => $athlete->athlete_number,
            'school' => $athlete->school,
            'status' => $athlete->status,
            'created_at' => $athlete->created_at,
            'updated_at' => $athlete->updated_at,
        ])->toArray();
    }

    /**
     * Fetch users by role (coach, official, tournament_manager).
     */
    private function getUsersByRole(string $role, ?string $search)
    {
        $query = User::with('school')->where('role', $role);
        if ($search) {
            $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        return $query->get()->map(fn($user) => (object)[
            'id' => $user->id,
            'type' => $role,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'school' => $user->school ? (object)[
                'id' => $user->school->id,
                'name' => $user->school->name,
                'short_name' => $user->school->short_name,
            ] : null,
            'status' => $user->status,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ])->toArray();
    }

    /**
     * Fetch all profile types combined.
     */
    private function getAllProfiles(?string $search)
    {
        $all = array_merge(
            $this->getSchools($search),
            $this->getAthletes($search),
            $this->getUsersByRole('coach', $search),
            $this->getUsersByRole('official', $search),
            $this->getUsersByRole('tournament_manager', $search)
        );

        // Sort descending by created_at
        usort($all, fn($a, $b) => strtotime($b->created_at) - strtotime($a->created_at));

        return $all;
    }

    /**
     * Paginate a raw array collection.
     */
    private function paginateCollection(array $collection, int $page, int $limit): array
    {
        $total = count($collection);
        $offset = ($page - 1) * $limit;
        $items = array_slice($collection, $offset, $limit);

        return [
            'data' => $items,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $limit),
            'per_page' => $limit,
            'total' => $total
        ];
    }
}
