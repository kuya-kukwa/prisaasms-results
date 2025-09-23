<?php

namespace App\Services\Layer1;

use App\Models\Layer1\School;

class SchoolService
{
    public function getAll(array $filters = [], $perPage = 10, $sortBy = 'name', $sortDir = 'asc')
    {
        $query = School::with(['province', 'tournaments']);

        // 🔍 Filters
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['province_id'])) {
            $query->where('province_id', $filters['province_id']);
        }

        // 🔽 Sorting
        if (in_array($sortBy, ['id', 'name', 'province_id', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getById($id)
    {
        return School::with(['province', 'tournaments'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return School::create($data);
    }

    public function update($id, array $data)
    {
        $school = School::findOrFail($id);
        $school->update($data);

        return $school->load(['province', 'tournaments']);
    }

    public function delete($id)
    {
        $school = School::findOrFail($id);
        return $school->delete(); // 🔥 soft delete
    }

    public function restore($id)
    {
        $school = School::onlyTrashed()->findOrFail($id);
        return $school->restore();
    }

    public function forceDelete($id)
    {
        $school = School::onlyTrashed()->findOrFail($id);
        return $school->forceDelete(); // permanent delete
    }
}
