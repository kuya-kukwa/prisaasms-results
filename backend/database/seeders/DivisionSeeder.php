<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('divisions')->insert([
            ['name' => 'Boys', 'code' => 'BOYS'],
            ['name' => 'Girls', 'code' => 'GIRLS'],
            ['name' => 'Men', 'code' => 'MEN'],
            ['name' => 'Women', 'code' => 'WOMEN'],
            ['name' => 'Mixed Youth', 'code' => 'MXY'],
            ['name' => 'Mixed Senior', 'code' => 'MXS'],
        ]);
    }
}
