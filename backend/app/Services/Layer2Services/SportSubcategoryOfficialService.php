<?php

namespace App\Services;

use App\Models\Layer1\User;
use App\Models\Layer2\SportSubcategory;
use Illuminate\Support\Facades\DB;

class SportSubcategoryOfficialService
{
    public function listOfficials(SportSubcategory $subcategory)
    {
        return $subcategory->officials()->get();
    }

    public function assignOfficial(SportSubcategory $subcategory, User $official)
    {
        DB::transaction(function () use ($subcategory, $official) {
            $subcategory->officials()->syncWithoutDetaching([$official->id]);
        });

        return $official;
    }

    public function removeOfficial(SportSubcategory $subcategory, User $official)
    {
        DB::transaction(function () use ($subcategory, $official) {
            $subcategory->officials()->detach($official->id);
        });

        return true;
    }
}
