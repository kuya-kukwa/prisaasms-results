<?php

namespace App\Services;

use App\Models\Layer2\Athlete;
use App\Models\Layer1\Division;

class AthleteEligibilityService
{
    public function checkDivisionEligibility(Athlete $athlete, Division $division): bool
    {
        $age = $athlete->age;

        switch (strtolower($division->name)) {
            case 'boys':
            case 'girls':
            case 'mixed youth':
                return $age < 18;

            case 'men':
            case 'women':
            case 'mixed senior':
                return $age >= 18;

            default:
                return true; // fallback (no restriction)
        }
    }
}
