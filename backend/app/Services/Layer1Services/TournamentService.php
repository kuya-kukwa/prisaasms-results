<?php

namespace App\Services\Layer1;

use App\Models\Layer1\Tournament;

class TournamentService
{
    public function getAll(array $filters = [], $perPage = 10, $sortBy = 'created_at', $sortDir = 'desc')
    {
        $query = Tournament::with([
            'seasonYear',
            'schools',
            'hostSchool',
            'hostProvince',
            'hostRegion'
        ]);

        // 🔍 Filters
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (!empty($filters['season_year_id'])) {
            $query->where('season_year_id', $filters['season_year_id']);
        }

        if (!empty($filters['host_school_id'])) {
            $query->where('host_school_id', $filters['host_school_id']);
        }

        if (!empty($filters['host_province_id'])) {
            $query->where('host_province_id', $filters['host_province_id']);
        }

        if (!empty($filters['host_region_id'])) {
            $query->where('host_region_id', $filters['host_region_id']);
        }

        // 🔽 Sorting
        if (in_array($sortBy, ['id', 'name', 'level', 'season_year_id', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        // 📄 Pagination
        return $query->paginate($perPage);
    }

    public function getById($id)
    {
        return Tournament::with([
            'seasonYear',
            'schools',
            'hostSchool',
            'hostProvince',
            'hostRegion'
        ])->findOrFail($id);
    }

    public function create(array $data)
    {
        $tournament = Tournament::create($data);

        if (!empty($data['school_ids'])) {
            $tournament->schools()->sync($data['school_ids']);
        }

        return $tournament->load([
            'seasonYear',
            'schools',
            'hostSchool',
            'hostProvince',
            'hostRegion'
        ]);
    }

    public function update($id, array $data)
    {
        $tournament = Tournament::findOrFail($id);
        $tournament->update($data);

        if (isset($data['school_ids'])) {
            $tournament->schools()->sync($data['school_ids']);
        }

        return $tournament->load([
            'seasonYear',
            'schools',
            'hostSchool',
            'hostProvince',
            'hostRegion'
        ]);
    }

    public function delete($id)
    {
        $tournament = Tournament::findOrFail($id);
        return $tournament->delete();
    }
}
