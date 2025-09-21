<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            [
                'name' => 'National Capital Region',
                'code' => 'NCR',
                'status' => 'active',
            ],
            [
                'name' => 'Cordillera Administrative Region',
                'code' => 'CAR',
                'status' => 'active',
            ],
            [
                'name' => 'Ilocos Region',
                'code' => 'I',
                'status' => 'active',
            ],
            [
                'name' => 'Cagayan Valley',
                'code' => 'II',
                'status' => 'active',
            ],
            [
                'name' => 'Central Luzon',
                'code' => 'III',
                'status' => 'active',
            ],
            [
                'name' => 'Calabarzon',
                'code' => 'IV-A',
                'status' => 'active',
            ],
            [
                'name' => 'Mimaropa',
                'code' => 'IV-B',
                'status' => 'active',
            ],
            [
                'name' => 'Bicol Region',
                'code' => 'V',
                'status' => 'active',
            ],
            [
                'name' => 'Western Visayas',
                'code' => 'VI',
                'status' => 'active',
            ],
            [
                'name' => 'Central Visayas',
                'code' => 'VII',
                'status' => 'active',
            ],
            [
                'name' => 'Eastern Visayas',
                'code' => 'VIII',
                'status' => 'active',
            ],
            [
                'name' => 'Zamboanga Peninsula',
                'code' => 'IX',
                'status' => 'active',
            ],
            [
                'name' => 'Northern Mindanao',
                'code' => 'X',
                'status' => 'active',
            ],
            [
                'name' => 'Davao Region',
                'code' => 'XI',
                'status' => 'active',
            ],
            [
                'name' => 'SOCCSKSARGEN',
                'code' => 'XII',
                'status' => 'active',
            ],
            [
                'name' => 'Caraga',
                'code' => 'XIII',
                'status' => 'active',
            ],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}