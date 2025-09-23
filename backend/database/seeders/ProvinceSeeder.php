<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Layer1\Province;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            // Region I: Ilocos Region
            ['name' => 'Ilocos Norte', 'region_id' => 1],
            ['name' => 'Ilocos Sur', 'region_id' => 1],
            ['name' => 'La Union', 'region_id' => 1],
            ['name' => 'Pangasinan', 'region_id' => 1],

            // Region II: Cagayan Valley
            ['name' => 'Batanes', 'region_id' => 2],
            ['name' => 'Cagayan', 'region_id' => 2],
            ['name' => 'Isabela', 'region_id' => 2],
            ['name' => 'Nueva Vizcaya', 'region_id' => 2],
            ['name' => 'Quirino', 'region_id' => 2],

            // Region III: Central Luzon
            ['name' => 'Aurora', 'region_id' => 3],
            ['name' => 'Bataan', 'region_id' => 3],
            ['name' => 'Bulacan', 'region_id' => 3],
            ['name' => 'Nueva Ecija', 'region_id' => 3],
            ['name' => 'Pampanga', 'region_id' => 3],
            ['name' => 'Tarlac', 'region_id' => 3],
            ['name' => 'Zambales', 'region_id' => 3],

            // Region IVA: CALABARZON
            ['name' => 'Batangas', 'region_id' => 4],
            ['name' => 'Cavite', 'region_id' => 4],
            ['name' => 'Laguna', 'region_id' => 4],
            ['name' => 'Quezon', 'region_id' => 4],
            ['name' => 'Rizal', 'region_id' => 4],

            // Region IVB: MIMAROPA
            ['name' => 'Marinduque', 'region_id' => 5],
            ['name' => 'Occidental Mindoro', 'region_id' => 5],
            ['name' => 'Oriental Mindoro', 'region_id' => 5],
            ['name' => 'Palawan', 'region_id' => 5],
            ['name' => 'Romblon', 'region_id' => 5],

            // Region V: Bicol Region
            ['name' => 'Albay', 'region_id' => 6],
            ['name' => 'Camarines Norte', 'region_id' => 6],
            ['name' => 'Camarines Sur', 'region_id' => 6],
            ['name' => 'Catanduanes', 'region_id' => 6],
            ['name' => 'Masbate', 'region_id' => 6],
            ['name' => 'Sorsogon', 'region_id' => 6],

            // Region VI: Western Visayas
            ['name' => 'Aklan', 'region_id' => 7],
            ['name' => 'Antique', 'region_id' => 7],
            ['name' => 'Capiz', 'region_id' => 7],
            ['name' => 'Guimaras', 'region_id' => 7],
            ['name' => 'Iloilo', 'region_id' => 7],
            ['name' => 'Negros Occidental', 'region_id' => 7],

            // Region VII: Central Visayas
            ['name' => 'Bohol', 'region_id' => 8],
            ['name' => 'Cebu', 'region_id' => 8],
            ['name' => 'Negros Oriental', 'region_id' => 8],
            ['name' => 'Siquijor', 'region_id' => 8],

            // Region VIII: Eastern Visayas
            ['name' => 'Biliran', 'region_id' => 9],
            ['name' => 'Eastern Samar', 'region_id' => 9],
            ['name' => 'Leyte', 'region_id' => 9],
            ['name' => 'Northern Samar', 'region_id' => 9],
            ['name' => 'Samar', 'region_id' => 9],
            ['name' => 'Southern Leyte', 'region_id' => 9],

            // Region IX: Zamboanga Peninsula
            ['name' => 'Zamboanga del Norte', 'region_id' => 10],
            ['name' => 'Zamboanga del Sur', 'region_id' => 10],
            ['name' => 'Zamboanga Sibugay', 'region_id' => 10],

            // Region X: Northern Mindanao
            ['name' => 'Bukidnon', 'region_id' => 11],
            ['name' => 'Camiguin', 'region_id' => 11],
            ['name' => 'Lanao del Norte', 'region_id' => 11],
            ['name' => 'Misamis Occidental', 'region_id' => 11],
            ['name' => 'Misamis Oriental', 'region_id' => 11],

            // Region XI: Davao Region
            ['name' => 'Davao de Oro', 'region_id' => 12],
            ['name' => 'Davao del Norte', 'region_id' => 12],
            ['name' => 'Davao del Sur', 'region_id' => 12],
            ['name' => 'Davao Occidental', 'region_id' => 12],
            ['name' => 'Davao Oriental', 'region_id' => 12],

            // Region XII: SOCCSKSARGEN
            ['name' => 'Cotabato', 'region_id' => 13],
            ['name' => 'Sarangani', 'region_id' => 13],
            ['name' => 'South Cotabato', 'region_id' => 13],
            ['name' => 'General Santos City', 'region_id' => 13], // technically city but include for mapping
            ['name' => 'Sultan Kudarat', 'region_id' => 13],

            // Region XIII: Caraga
            ['name' => 'Agusan del Norte', 'region_id' => 14],
            ['name' => 'Agusan del Sur', 'region_id' => 14],
            ['name' => 'Dinagat Islands', 'region_id' => 14],
            ['name' => 'Surigao del Norte', 'region_id' => 14],
            ['name' => 'Surigao del Sur', 'region_id' => 14],

            // BARMM
            ['name' => 'Basilan', 'region_id' => 15],
            ['name' => 'Lanao del Sur', 'region_id' => 15],
            ['name' => 'Maguindanao', 'region_id' => 15],
            ['name' => 'Sulu', 'region_id' => 15],
            ['name' => 'Tawi-Tawi', 'region_id' => 15],

            // CAR
            ['name' => 'Abra', 'region_id' => 16],
            ['name' => 'Apayao', 'region_id' => 16],
            ['name' => 'Benguet', 'region_id' => 16],
            ['name' => 'Ifugao', 'region_id' => 16],
            ['name' => 'Kalinga', 'region_id' => 16],
            ['name' => 'Mountain Province', 'region_id' => 16],

            // NCR
            ['name' => 'Metro Manila', 'region_id' => 17],

            // SAR
            ['name' => 'Special Administrative Region', 'region_id' => 18]
        ];

        foreach ($provinces as $province) {
            Province::create($province);
        }
    }
}
