<?php

namespace App\Services\Layer1;

use App\Models\Layer1\SeasonYear;

class SeasonYearService
{
    public function getAll(array $filters = [], $perPage = 10, $sortBy = 'year', $sortDir = 'desc')
    {
        $query = SeasonYear::with('tournaments');

        // 🔍 Filters
        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        if (isset($filters['active'])) {
            $query->where('active', (bool) $filters['active']);
        }

        // 🔽 Sorting
        if (in_array($sortBy, ['id', 'year', 'active', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getById($id)
    {
        return SeasonYear::with('tournaments')->findOrFail($id);
    }

    public function create(array $data)
    {
        // Kapag gagawa ng bagong active season year, i-deactivate yung iba
        if (!empty($data['active']) && $data['active'] == true) {
            SeasonYear::where('active', true)->update(['active' => false]);
        }

        return SeasonYear::create($data);
    }

    public function update($id, array $data)
    {
        $seasonYear = SeasonYear::findOrFail($id);

        if (isset($data['active']) && $data['active'] == true) {
            // i-deactivate lahat ng iba
            SeasonYear::where('id', '!=', $id)->update(['active' => false]);
        }

        $seasonYear->update($data);
        return $seasonYear->load('tournaments');
    }

    public function delete($id)
    {
        $seasonYear = SeasonYear::findOrFail($id);
        return $seasonYear->delete();
    }
}
