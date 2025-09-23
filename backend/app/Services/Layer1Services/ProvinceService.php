<?php

namespace App\Services\Layer1;

use App\Models\Layer1\Province;

class ProvinceService
{
    public function getAll(array $filters = [], $perPage = 10, $sortBy = 'name', $sortDir = 'asc')
    {
        $query = Province::with(['region', 'schools']);

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
        }

        if (in_array($sortBy, ['id', 'name', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getById($id)
    {
        return Province::with(['region', 'schools'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Province::create($data);
    }

    public function update($id, array $data)
    {
        $province = Province::findOrFail($id);
        $province->update($data);

        return $province->load(['region', 'schools']);
    }

    public function restore($id)
{
    $province = Province::onlyTrashed()->findOrFail($id);
    return $province->restore();
}

public function forceDelete($id)
{
    $province = Province::onlyTrashed()->findOrFail($id);
    return $province->forceDelete();
}

    public function delete($id)
    {
        $province = Province::findOrFail($id);
        return $province->delete(); // 🔥 hard delete
    }
}
