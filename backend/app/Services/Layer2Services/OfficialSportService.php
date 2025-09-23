<?php

namespace App\Services\Layer2;

use App\Models\Layer2\OfficialSport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OfficialSportService
{
    /**
     * List with pagination, filtering & sorting
     */
    public function list(array $filters = [], int $perPage = 10, string $sortBy = 'id', string $sortDir = 'asc'): LengthAwarePaginator
    {
        $query = OfficialSport::with(['official', 'sport']);

        if (!empty($filters['official_id'])) {
            $query->where('official_id', $filters['official_id']);
        }

        if (!empty($filters['sport_id'])) {
            $query->where('sport_id', $filters['sport_id']);
        }

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    }

    /**
     * Assign sport to official
     */
    public function assign(array $data): OfficialSport
    {
        return OfficialSport::create($data);
    }

    /**
     * Update assignment
     */
    public function update(OfficialSport $officialSport, array $data): OfficialSport
    {
        $officialSport->update($data);
        return $officialSport;
    }

    /**
     * Remove assignment (soft delete)
     */
    public function delete(OfficialSport $officialSport): bool
    {
        return (bool) $officialSport->delete();
    }
}
