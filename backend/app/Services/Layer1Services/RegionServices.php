<?php

namespace App\Services\Layer1;

use App\Models\Layer1\Region;

class RegionService
{
    public function getAll(array $filters = [], $perPage = 10, $sortBy = 'name', $sortDir = 'asc')
    {
        $query = Region::with(['provinces', 'schools']);

        // 🔍 Filters
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['code'])) {
            $query->where('code', 'like', '%' . $filters['code'] . '%');
        }

        // 🔽 Sorting
        if (in_array($sortBy, ['id', 'name', 'code', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getById($id)
    {
        return Region::with(['provinces', 'schools'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Region::create($data);
    }

    public function update($id, array $data)
    {
        $region = Region::findOrFail($id);
        $region->update($data);

        return $region->load(['provinces', 'schools']);
    }

    public function delete($id)
    {
        $region = Region::findOrFail($id);
        return $region->delete(); // 🔥 soft delete
    }

    public function restore($id)
    {
        $region = Region::onlyTrashed()->findOrFail($id);
        return $region->restore();
    }

    public function forceDelete($id)
    {
        $region = Region::onlyTrashed()->findOrFail($id);
        return $region->forceDelete();
    }
}
