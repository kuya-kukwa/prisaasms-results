<?php

namespace App\Services;

use App\Models\Layer1\User;
use App\Models\Layer2\Sport;
use Illuminate\Support\Facades\DB;

class SportOfficialService
{
    /**
     * Get all officials assigned to a sport.
     */
    public function listOfficials(Sport $sport)
    {
        return $sport->officials()->get();
    }

    /**
     * Assign an official to a sport.
     */
    public function assignOfficial(Sport $sport, User $official)
    {
        DB::transaction(function () use ($sport, $official) {
            $sport->officials()->syncWithoutDetaching([$official->id]);
        });

        return $official;
    }

    /**
     * Remove an official from a sport.
     */
    public function removeOfficial(Sport $sport, User $official)
    {
        DB::transaction(function () use ($sport, $official) {
            $sport->officials()->detach($official->id);
        });

        return true;
    }
}
