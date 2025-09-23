<?php

namespace App\Services\Layer1;

use App\Models\Layer1\Division;

class DivisionService
{
    public function getAll(array $filters = [], $perPage = 10, $sortBy = 'name', $sortDir = 'asc')
    {
        $query = Division::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (in_array($sortBy, ['id', 'name', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getById($id)
    {
        return Division::findOrFail($id);
    }

    public function create(array $data)
    {
        return Division::create($data);
    }

    public function update($id, array $data)
    {
        $division = Division::findOrFail($id);
        $division->update($data);

        return $division;
    }

    public function delete($id)
    {
        $division = Division::findOrFail($id);
        return $division->delete(); // 🔥 soft delete
    }

    public function restore($id)
    {
        $division = Division::onlyTrashed()->findOrFail($id);
        return $division->restore();
    }

    public function forceDelete($id)
    {
        $division = Division::onlyTrashed()->findOrFail($id);
        return $division->forceDelete();
    }
}
