<?php

namespace App\Services;

use App\Models\Layer2\Athlete;

class AthleteService
{
    public function listAthletes(array $filters = [], int $perPage = 15, string $sortBy = 'last_name', string $sortDir = 'asc')
    {
        $query = Athlete::with(['school', 'division', 'sports', 'teams']);

        // 🔍 filters
        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }
        if (!empty($filters['division_id'])) {
            $query->where('division_id', $filters['division_id']);
        }
        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('last_name', 'like', '%' . $filters['search'] . '%');
            });
        }

        // 📑 sorting & pagination
        return $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    }

    public function createAthlete(array $data): Athlete
    {
        return Athlete::create($data);
    }

    public function updateAthlete(Athlete $athlete, array $data): Athlete
    {
        $athlete->update($data);
        return $athlete;
    }

    public function deleteAthlete(Athlete $athlete): bool
    {
        return $athlete->delete();
    }
}
