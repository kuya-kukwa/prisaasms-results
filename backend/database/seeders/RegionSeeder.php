<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Layer1\Region;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['name' => 'Ilocos Region', 'code' => 'I'],
            ['name' => 'Cagayan Valley', 'code' => 'II'],
            ['name' => 'Central Luzon', 'code' => 'III'],
            ['name' => 'CALABARZON', 'code' => 'IVA'],
            ['name' => 'MIMAROPA', 'code' => 'IVB'],
            ['name' => 'Bicol Region', 'code' => 'V'],
            ['name' => 'Western Visayas', 'code' => 'VI'],
            ['name' => 'Central Visayas', 'code' => 'VII'],
            ['name' => 'Eastern Visayas', 'code' => 'VIII'],
            ['name' => 'Zamboanga Peninsula', 'code' => 'IX'],
            ['name' => 'Northern Mindanao', 'code' => 'X'],
            ['name' => 'Davao Region', 'code' => 'XI'],
            ['name' => 'SOCCSKSARGEN', 'code' => 'XII'],
            ['name' => 'Caraga', 'code' => 'XIII'],
            ['name' => 'Bangsamoro Autonomous Region in Muslim Mindanao', 'code' => 'BARMM'],
            ['name' => 'Cordillera Administrative Region', 'code' => 'CAR'],
            ['name' => 'National Capital Region', 'code' => 'NCR'],
            ['name' => 'Special Administrative Region', 'code' => 'SAR']
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}
