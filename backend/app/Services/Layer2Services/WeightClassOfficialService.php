<?php

namespace App\Services;

use App\Models\Layer1\User;
use App\Models\Layer2\WeightClass;
use Illuminate\Support\Facades\DB;

class WeightClassOfficialService
{
    public function listOfficials(WeightClass $weightClass)
    {
        return $weightClass->officials()->get();
    }

    public function assignOfficial(WeightClass $weightClass, User $official)
    {
        DB::transaction(function () use ($weightClass, $official) {
            $weightClass->officials()->syncWithoutDetaching([$official->id]);
        });

        return $official;
    }

    public function removeOfficial(WeightClass $weightClass, User $official)
    {
        DB::transaction(function () use ($weightClass, $official) {
            $weightClass->officials()->detach($official->id);
        });

        return true;
    }
}
