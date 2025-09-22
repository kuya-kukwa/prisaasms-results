<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionsSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name' => 'Boys',  'label' => 'B'],
            ['name' => 'Girls', 'label' => 'G'],
            ['name' => 'Men',   'label' => 'M'],
            ['name' => 'Women', 'label' => 'W'],
        ];

        foreach ($divisions as $division) {
            Division::updateOrCreate(
                ['name' => $division['name']],
                ['label' => $division['label']]
            );
        }
    }
}
