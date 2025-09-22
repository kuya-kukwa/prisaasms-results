<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionsSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['name' => 'National Capital Region', 'code' => 'NCR'],
            ['name' => 'Cordillera Administrative Region', 'code' => 'CAR'],
            ['name' => 'Ilocos Region', 'code' => 'I'],
            ['name' => 'Cagayan Valley', 'code' => 'II'],
            ['name' => 'Central Luzon', 'code' => 'III'],
            ['name' => 'Calabarzon', 'code' => 'IV-A'],
            ['name' => 'Mimaropa', 'code' => 'IV-B'],
            ['name' => 'Bicol Region', 'code' => 'V'],
            ['name' => 'Western Visayas', 'code' => 'VI'],
            ['name' => 'Central Visayas', 'code' => 'VII'],
            ['name' => 'Eastern Visayas', 'code' => 'VIII'],
            ['name' => 'Zamboanga Peninsula', 'code' => 'IX'],
            ['name' => 'Northern Mindanao', 'code' => 'X'],
            ['name' => 'Davao Region', 'code' => 'XI'],
            ['name' => 'SOCCSKSARGEN', 'code' => 'XII'],
            ['name' => 'Caraga', 'code' => 'XIII'],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(
                ['code' => $region['code']],
                ['name' => $region['name'], 'status' => 'active']
            );
        }
    }
}
