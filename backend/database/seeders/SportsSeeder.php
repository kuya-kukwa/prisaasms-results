<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sport;

class SportsSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            ['name' => 'Basketball', 'type' => 'team'],
            ['name' => 'Volleyball', 'type' => 'team'],
            ['name' => 'Football', 'type' => 'team'],
            ['name' => 'Athletics', 'type' => 'individual'],
            ['name' => 'Swimming', 'type' => 'individual'],
            ['name' => 'Table Tennis', 'type' => 'individual'],
            ['name' => 'Taekwondo', 'type' => 'individual'],
        ];

        foreach ($sports as $sport) {
            Sport::updateOrCreate(
                ['name' => $sport['name']],
                ['type' => $sport['type']]
            );
        }
    }
}
