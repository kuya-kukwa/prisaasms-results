<?php

namespace App\Services\Layer1;

use App\Models\Layer1\Venue;

class VenueService
{
    public function list(array $filters = [], int $perPage = 15)
    {
        $query = Venue::query();

        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (!empty($filters['province_id'])) {
            $query->where('province_id', $filters['province_id']);
        }

        if (!empty($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
        }

        return $query->paginate($perPage);
    }

    public function find(int $id)
    {
        return Venue::with(['school', 'province', 'region'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Venue::create($data);
    }

    public function update(Venue $venue, array $data)
    {
        $venue->update($data);
        return $venue;
    }

    public function delete(Venue $venue)
    {
        return $venue->delete();
    }
}
