<?php

namespace Database\Seeders;

use App\Models\Tournament;
use App\Models\Region;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get regions and venues for reference
        $regions = Region::all()->keyBy('code');
        $venues = Venue::all()->keyBy('name');

        $tournaments = [
            // 2025 National Games - Real PRISAA Event
            [
                'name' => '2025 PRISAA National Games',
                'short_name' => '2025 National Games',
                'tournament_code' => 'PNG2025',
                'description' => 'The 2025 PRISAA National Games hosted in Tuguegarao City, Cagayan Valley, featuring athletes from all 17 regions competing in multiple sports categories',
                'type' => 'championship',
                'level' => 'national',
                'scope' => 'national',
                'start_date' => Carbon::create(2025, 4, 3),
                'end_date' => Carbon::create(2025, 4, 11),
                'registration_end' => Carbon::create(2025, 3, 15),
                'host_location' => 'University of Cagayan Valley, Tuguegarao City, Cagayan Valley',
                'host_region_id' => $regions['II']->id ?? null,
                'sports_included' => ['Basketball', 'Volleyball', 'Athletics (Track and Field)', 'Swimming', 'Badminton', 'Table Tennis', 'Chess', 'Taekwondo', 'Arnis'],
                'status' => 'registration_open',
                'is_public' => true,
                'has_medal_tally' => true,
            ],

            // National Level Championships
            [
                'name' => 'PRISAA National Basketball Championship 2025',
                'short_name' => 'Nat\'l Basketball 2025',
                'tournament_code' => 'NB2025',
                'description' => 'National level basketball competition for private schools across the Philippines',
                'type' => 'championship',
                'level' => 'national',
                'scope' => 'national',
                'start_date' => Carbon::create(2025, 3, 15),
                'end_date' => Carbon::create(2025, 3, 22),
                'registration_end' => Carbon::create(2025, 2, 28),
                'host_location' => 'Rizal Memorial Sports Complex, Manila',
                'host_region_id' => $regions['NCR']->id ?? null,
                'sports_included' => ['Basketball'],
                'status' => 'registration_open',
                'is_public' => true,
                'has_medal_tally' => true,
            ],
            [
                'name' => 'PRISAA National Volleyball Championship 2025',
                'short_name' => 'Nat\'l Volleyball 2025',
                'tournament_code' => 'NV2025',
                'description' => 'National volleyball competition featuring the best private school teams',
                'type' => 'championship',
                'level' => 'national',
                'scope' => 'national',
                'start_date' => Carbon::create(2025, 4, 10),
                'end_date' => Carbon::create(2025, 4, 17),
                'registration_end' => Carbon::create(2025, 3, 25),
                'host_location' => 'Cebu City Sports Center, Cebu City',
                'host_region_id' => $regions['VII']->id ?? null,
                'sports_included' => ['Volleyball'],
                'status' => 'registration_open',
                'is_public' => true,
                'has_medal_tally' => true,
            ],
            [
                'name' => 'PRISAA National Swimming Championship 2025',
                'short_name' => 'Nat\'l Swimming 2025',
                'tournament_code' => 'NS2025',
                'description' => 'National swimming meet for private school athletes',
                'type' => 'championship',
                'level' => 'national',
                'scope' => 'national',
                'start_date' => Carbon::create(2025, 5, 5),
                'end_date' => Carbon::create(2025, 5, 8),
                'registration_end' => Carbon::create(2025, 4, 20),
                'host_location' => 'Rizal Memorial Sports Complex, Manila',
                'host_region_id' => $regions['NCR']->id ?? null,
                'sports_included' => ['Swimming'],
                'status' => 'registration_open',
                'is_public' => true,
                'has_medal_tally' => true,
            ],

            // Regional Level Tournaments - Bicol Region
            [
                'name' => 'PRISAA Region V Basketball Championship 2025',
                'short_name' => 'Reg V Basketball 2025',
                'tournament_code' => 'RV5B2025',
                'description' => 'Regional basketball championship for Bicol private schools',
                'type' => 'championship',
                'level' => 'regional',
                'scope' => 'single_region',
                'start_date' => Carbon::create(2025, 1, 20),
                'end_date' => Carbon::create(2025, 1, 25),
                'registration_end' => Carbon::create(2025, 1, 10),
                'host_location' => 'Bicol University Gymnasium, Legazpi City',
                'host_region_id' => $regions['V']->id ?? null,
                'sports_included' => ['Basketball'],
                'status' => 'registration_open',
                'is_public' => true,
                'has_medal_tally' => true,
            ],
            [
                'name' => 'PRISAA Region V Volleyball Championship 2025',
                'short_name' => 'Reg V Volleyball 2025',
                'tournament_code' => 'RV5V2025',
                'description' => 'Regional volleyball competition for Bicol schools',
                'type' => 'championship',
                'level' => 'regional',
                'scope' => 'single_region',
                'start_date' => Carbon::create(2025, 2, 10),
                'end_date' => Carbon::create(2025, 2, 15),
                'registration_end' => Carbon::create(2025, 1, 30),
                'host_location' => 'Divine Word College of Legazpi, Legazpi City',
                'host_region_id' => $regions['V']->id ?? null,
                'sports_included' => ['Volleyball'],
                'status' => 'registration_open',
                'is_public' => true,
                'has_medal_tally' => true,
            ],

            // Provincial Level Tournaments - Sorsogon Province
            [
                'name' => 'PRISAA Sorsogon Provincial Meet 2025',
                'short_name' => 'Sorsogon Provincial 2025',
                'tournament_code' => 'SOR2025',
                'description' => 'Provincial multi-sport competition for Sorsogon private schools',
                'type' => 'championship',
                'level' => 'provincial',
                'scope' => 'single_region',
                'start_date' => Carbon::create(2024, 11, 15),
                'end_date' => Carbon::create(2024, 11, 20),
                'registration_end' => Carbon::create(2024, 11, 5),
                'host_location' => 'Sorsogon State University Sports Complex, Sorsogon City',
                'host_region_id' => $regions['V']->id ?? null,
                'sports_included' => ['Basketball', 'Volleyball', 'Athletics (Track and Field)', 'Swimming'],
                'status' => 'completed',
                'is_public' => true,
                'has_medal_tally' => true,
            ],

            // Central Visayas Regional
            [
                'name' => 'PRISAA Region VII Multi-Sports Championship 2025',
                'short_name' => 'Reg VII Multi-Sports 2025',
                'tournament_code' => 'RV7MS2025',
                'description' => 'Regional multi-sport championship for Central Visayas private schools',
                'type' => 'championship',
                'level' => 'regional',
                'scope' => 'single_region',
                'start_date' => Carbon::create(2025, 3, 1),
                'end_date' => Carbon::create(2025, 3, 8),
                'registration_end' => Carbon::create(2025, 2, 15),
                'host_location' => 'Cebu City Sports Center, Cebu City',
                'host_region_id' => $regions['VII']->id ?? null,
                'sports_included' => ['Basketball', 'Volleyball', 'Swimming', 'Athletics (Track and Field)'],
                'status' => 'registration_open',
                'is_public' => true,
                'has_medal_tally' => true,
            ],

            // Davao Region
            [
                'name' => 'PRISAA Region XI Basketball Championship 2025',
                'short_name' => 'Reg XI Basketball 2025',
                'tournament_code' => 'RV11B2025',
                'description' => 'Regional basketball championship for Davao Region private schools',
                'type' => 'championship',
                'level' => 'regional',
                'scope' => 'single_region',
                'start_date' => Carbon::create(2025, 4, 20),
                'end_date' => Carbon::create(2025, 4, 25),
                'registration_end' => Carbon::create(2025, 4, 10),
                'host_location' => 'Davao City Sports Complex, Davao City',
                'host_region_id' => $regions['XI']->id ?? null,
                'sports_included' => ['Basketball'],
                'status' => 'registration_open',
                'is_public' => true,
                'has_medal_tally' => true,
            ],

            // NCR Regional
            [
                'name' => 'PRISAA NCR Volleyball Championship 2025',
                'short_name' => 'NCR Volleyball 2025',
                'tournament_code' => 'NCRV2025',
                'description' => 'NCR regional volleyball championship for Metro Manila private schools',
                'type' => 'championship',
                'level' => 'regional',
                'scope' => 'single_region',
                'start_date' => Carbon::create(2025, 2, 25),
                'end_date' => Carbon::create(2025, 3, 2),
                'registration_end' => Carbon::create(2025, 2, 15),
                'host_location' => 'PhilSports Arena, Pasig City',
                'host_region_id' => $regions['NCR']->id ?? null,
                'sports_included' => ['Volleyball'],
                'status' => 'registration_open',
                'is_public' => true,
                'has_medal_tally' => true,
            ],

            // Completed tournaments for historical data - Real PRISAA Events
            [
                'name' => 'PRISAA National Athletics Championship 2024',
                'short_name' => 'Nat\'l Athletics 2024',
                'tournament_code' => 'NA2024',
                'description' => '2024 National track and field championship held at UP Diliman',
                'type' => 'championship',
                'level' => 'national',
                'scope' => 'national',
                'start_date' => Carbon::create(2024, 5, 10),
                'end_date' => Carbon::create(2024, 5, 15),
                'registration_end' => Carbon::create(2024, 4, 25),
                'host_location' => 'University of the Philippines Diliman, Quezon City',
                'host_region_id' => $regions['NCR']->id ?? null,
                'sports_included' => ['Athletics (Track and Field)'],
                'status' => 'completed',
                'is_public' => true,
                'has_medal_tally' => true,
            ],
            [
                'name' => 'PRISAA Bohol Taekwondo Championship 2024',
                'short_name' => 'Bohol Taekwondo 2024',
                'tournament_code' => 'BOHTKD2024',
                'description' => 'Regional Taekwondo championship held in Bohol featuring champions from various schools',
                'type' => 'championship',
                'level' => 'regional',
                'scope' => 'single_region',
                'start_date' => Carbon::create(2024, 9, 10),
                'end_date' => Carbon::create(2024, 9, 15),
                'registration_end' => Carbon::create(2024, 8, 25),
                'host_location' => 'Bohol Provincial Sports Complex',
                'host_region_id' => $regions['VII']->id ?? null,
                'sports_included' => ['Taekwondo'],
                'status' => 'completed',
                'is_public' => true,
                'has_medal_tally' => true,
            ],
            [
                'name' => 'PRISAA Camarines Sur Mutya ng PRISAA 2024',
                'short_name' => 'Camarines Sur Mutya 2024',
                'tournament_code' => 'CSMUTYA2024',
                'description' => 'Beauty pageant competition for PRISAA featuring candidates from Camarines Sur schools',
                'type' => 'invitational',
                'level' => 'regional',
                'scope' => 'single_region',
                'start_date' => Carbon::create(2024, 9, 5),
                'end_date' => Carbon::create(2024, 9, 5),
                'registration_end' => Carbon::create(2024, 8, 20),
                'host_location' => 'Naga College Foundation, Camarines Sur',
                'host_region_id' => $regions['V']->id ?? null,
                'sports_included' => ['Mutya ng PRISAA'],
                'status' => 'completed',
                'is_public' => true,
                'has_medal_tally' => false,
            ],
        ];

        foreach ($tournaments as $tournament) {
            Tournament::create($tournament);
        }
    }
}
