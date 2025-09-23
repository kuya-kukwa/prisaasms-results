<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venues = [
            // National Level Venues - Real PRISAA Venues
            [
                'name' => 'University of Cagayan Valley',
                'short_name' => 'UCV',
                'venue_code' => 'UCV2025',
                'address' => 'University of Cagayan Valley, Tuguegarao City, Cagayan Valley',
                'region_id' => $regions['II']->id ?? null,
                'contact_person' => 'PRISAA National Committee',
                'contact_number' => '+63 78 396 1987',
                'email' => 'national@prisaasportsfoundation.com',
                'venue_type' => 'covered',
                'venue_category' => 'stadium',
                'status' => 'active',
            ],
            [
                'name' => 'Rizal Memorial Sports Complex',
                'short_name' => 'Rizal Memorial',
                'venue_code' => 'RMSC',
                'address' => 'Pablo Ocampo Sr. Street, Malate, Manila',
                'region_id' => $regions['NCR']->id ?? null,
                'contact_person' => 'Rizal Memorial Management',
                'contact_number' => '+63 2 8527 4061',
                'email' => 'info@rizalmemorial.com',
                'venue_type' => 'covered',
                'venue_category' => 'stadium',
                'status' => 'active',
            ],
            [
                'name' => 'PhilSports Arena',
                'short_name' => 'PhilSports',
                'venue_code' => 'PSA',
                'address' => 'Meralco Avenue, Pasig City',
                'region_id' => $regions['NCR']->id ?? null,
                'contact_person' => 'PhilSports Management',
                'contact_number' => '+63 2 8631 8001',
                'email' => 'info@philsports.gov.ph',
                'venue_type' => 'indoor',
                'venue_category' => 'gymnasium',
                'status' => 'active',
            ],

            // Regional Venues - Bicol Region
            [
                'name' => 'Bicol University Gymnasium',
                'short_name' => 'BU Gym',
                'venue_code' => 'BUG',
                'address' => 'Rizal Street, Legazpi City, Albay',
                'region_id' => $regions['V']->id ?? null,
                'contact_person' => 'BU Sports Committee',
                'contact_number' => '+63 52 820 2382',
                'email' => 'sports@bicol-u.edu.ph',
                'venue_type' => 'indoor',
                'venue_category' => 'gymnasium',
                'status' => 'active',
            ],
            [
                'name' => 'Divine Word College of Legazpi',
                'short_name' => 'DWCL',
                'venue_code' => 'DWCL',
                'address' => 'Peñafrancia Avenue, Legazpi City, Albay',
                'region_id' => $regions['V']->id ?? null,
                'contact_person' => 'DWCL Sports Coordinator',
                'contact_number' => '+63 52 742 2346',
                'email' => 'sports@dwcl.edu.ph',
                'venue_type' => 'covered',
                'venue_category' => 'court',
                'status' => 'active',
            ],
            [
                'name' => 'Sorsogon State University Sports Complex',
                'short_name' => 'SSU Sports',
                'venue_code' => 'SSUSC',
                'address' => 'Sorsogon State University, Sorsogon City',
                'region_id' => $regions['V']->id ?? null,
                'contact_person' => 'SSU Sports Committee',
                'contact_number' => '+63 56 211 4272',
                'email' => 'sports@ssu.edu.ph',
                'venue_type' => 'covered',
                'venue_category' => 'stadium',
                'status' => 'active',
            ],

            // Visayas Region Venues
            [
                'name' => 'Cebu City Sports Center',
                'short_name' => 'CCSC',
                'venue_code' => 'CCSC',
                'address' => 'Cebu City Sports Center, Cebu City',
                'region_id' => $regions['VII']->id ?? null,
                'contact_person' => 'CCSC Management',
                'contact_number' => '+63 32 253 1111',
                'email' => 'info@cebucitysports.gov.ph',
                'venue_type' => 'covered',
                'venue_category' => 'stadium',
                'status' => 'active',
            ],
            [
                'name' => 'STI West Negros University',
                'short_name' => 'STI WNU',
                'venue_code' => 'STIWNU',
                'address' => 'STI West Negros University, Bacolod City',
                'region_id' => $regions['VI']->id ?? null,
                'contact_person' => 'STI WNU Sports Committee',
                'contact_number' => '+63 34 709 1111',
                'email' => 'sports@westnegrosuni.edu.ph',
                'venue_type' => 'covered',
                'venue_category' => 'stadium',
                'status' => 'active',
            ],

            // Mindanao Region Venues
            [
                'name' => 'Davao City Sports Complex',
                'short_name' => 'DCSC',
                'venue_code' => 'DCSC',
                'address' => 'Davao City Sports Complex, Davao City',
                'region_id' => $regions['XI']->id ?? null,
                'contact_person' => 'DCSC Management',
                'contact_number' => '+63 82 221 1111',
                'email' => 'info@davaosports.gov.ph',
                'venue_type' => 'covered',
                'venue_category' => 'stadium',
                'status' => 'active',
            ],
            [
                'name' => 'University of the Philippines Diliman',
                'short_name' => 'UP Diliman',
                'venue_code' => 'UPD',
                'address' => 'University of the Philippines Diliman, Quezon City',
                'region_id' => $regions['NCR']->id ?? null,
                'contact_person' => 'UP Diliman Sports Committee',
                'contact_number' => '+63 2 8981 8501',
                'email' => 'sports@upd.edu.ph',
                'venue_type' => 'outdoor',
                'venue_category' => 'track',
                'status' => 'active',
            ],
        ];

        foreach ($venues as $venue) {
            Venue::create($venue);
        }
    }
}