<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Region;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get regions for reference
        $regions = Region::all()->keyBy('code');

        $schools = [
            // REGION I (Ilocos Region) - PRISAA Participating Schools
            [
                'name' => 'University of Northern Philippines',
                'short_name' => 'UNP',
                'address' => 'Tamag, Vigan City, Ilocos Sur',
                'region_id' => $regions['I']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Don Mariano Marcos Memorial State University',
                'short_name' => 'DMMMSU',
                'address' => 'Bacnotan, La Union',
                'region_id' => $regions['I']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Pangasinan State University',
                'short_name' => 'PSU',
                'address' => 'Lingayen, Pangasinan',
                'region_id' => $regions['I']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Mariano Marcos State University',
                'short_name' => 'MMSU',
                'address' => 'Batac, Ilocos Norte',
                'region_id' => $regions['I']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Ilocos Sur Polytechnic State College',
                'short_name' => 'ISPSC',
                'address' => 'Tagudin, Ilocos Sur',
                'region_id' => $regions['I']->id ?? null,
                'status' => 'active',
            ],

            // REGION II (Cagayan Valley) - PRISAA Participating Schools
            [
                'name' => 'University of Cagayan Valley',
                'short_name' => 'UCV',
                'address' => 'Tuguegarao City, Cagayan',
                'region_id' => $regions['II']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Isabela State University',
                'short_name' => 'ISU',
                'address' => 'Echague, Isabela',
                'region_id' => $regions['II']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Cagayan State University',
                'short_name' => 'CSU',
                'address' => 'Tuguegarao City, Cagayan',
                'region_id' => $regions['II']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Nueva Vizcaya State University',
                'short_name' => 'NVSU',
                'address' => 'Bayombong, Nueva Vizcaya',
                'region_id' => $regions['II']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Quirino State University',
                'short_name' => 'QSU',
                'address' => 'Diffun, Quirino',
                'region_id' => $regions['II']->id ?? null,
                'status' => 'active',
            ],

            // REGION III (Central Luzon) - PRISAA Participating Schools
            [
                'name' => 'Central Luzon State University',
                'short_name' => 'CLSU',
                'address' => 'Science City of Muñoz, Nueva Ecija',
                'region_id' => $regions['III']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Bulacan State University',
                'short_name' => 'BulSU',
                'address' => 'Malolos, Bulacan',
                'region_id' => $regions['III']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Nueva Ecija University of Science and Technology',
                'short_name' => 'NEUST',
                'address' => 'Cabanatuan City, Nueva Ecija',
                'region_id' => $regions['III']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Pampanga State Agricultural University',
                'short_name' => 'PSAU',
                'address' => 'Magalang, Pampanga',
                'region_id' => $regions['III']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Tarlac State University',
                'short_name' => 'TSU',
                'address' => 'Tarlac City, Tarlac',
                'region_id' => $regions['III']->id ?? null,
                'status' => 'active',
            ],

            // REGION IV-A (CALABARZON) - PRISAA Participating Schools
            [
                'name' => 'University of the Philippines Los Baños',
                'short_name' => 'UPLB',
                'address' => 'College, Laguna',
                'region_id' => $regions['IV-A']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'De La Salle University-Dasmariñas',
                'short_name' => 'DLSU-D',
                'address' => 'Dasmariñas, Cavite',
                'region_id' => $regions['IV-A']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Batangas State University',
                'short_name' => 'BatSU',
                'address' => 'Batangas City, Batangas',
                'region_id' => $regions['IV-A']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Laguna State Polytechnic University',
                'short_name' => 'LSPU',
                'address' => 'Santa Cruz, Laguna',
                'region_id' => $regions['IV-A']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Cavite State University',
                'short_name' => 'CvSU',
                'address' => 'Indang, Cavite',
                'region_id' => $regions['IV-A']->id ?? null,
                'status' => 'active',
            ],

            // REGION V (Bicol Region) - PRISAA Participating Schools
            [
                'name' => 'Bicol University',
                'short_name' => 'BU',
                'address' => 'Rizal Street, Legazpi City, Albay',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Saint Anthony',
                'short_name' => 'USA',
                'address' => 'Iriga City, Camarines Sur',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Sorsogon State University',
                'short_name' => 'SSU',
                'address' => 'Sorsogon City, Sorsogon',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Camarines Sur Polytechnic Colleges',
                'short_name' => 'CSPC',
                'address' => 'Nabua, Camarines Sur',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Catanduanes State University',
                'short_name' => 'CatSU',
                'address' => 'Virac, Catanduanes',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],

            // REGION VI (Western Visayas) - PRISAA Participating Schools
            [
                'name' => 'University of the Philippines Visayas',
                'short_name' => 'UPV',
                'address' => 'Miagao, Iloilo',
                'region_id' => $regions['VI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'West Visayas State University',
                'short_name' => 'WVSU',
                'address' => 'La Paz, Iloilo City',
                'region_id' => $regions['VI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Iloilo Science and Technology University',
                'short_name' => 'ISAT-U',
                'address' => 'La Paz, Iloilo City',
                'region_id' => $regions['VI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Central Philippine University',
                'short_name' => 'CPU',
                'address' => 'Jaro, Iloilo City',
                'region_id' => $regions['VI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Antique',
                'short_name' => 'UA',
                'address' => 'Sibalum, Antique',
                'region_id' => $regions['VI']->id ?? null,
                'status' => 'active',
            ],

            // REGION VII (Central Visayas) - PRISAA Participating Schools
            [
                'name' => 'University of San Carlos',
                'short_name' => 'USC',
                'address' => 'P. del Rosario Street, Cebu City',
                'region_id' => $regions['VII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Cebu Technological University',
                'short_name' => 'CTU',
                'address' => 'Cebu City, Cebu',
                'region_id' => $regions['VII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Cebu',
                'short_name' => 'UC',
                'address' => 'Cebu City, Cebu',
                'region_id' => $regions['VII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Cebu Normal University',
                'short_name' => 'CNU',
                'address' => 'Osmeña Boulevard, Cebu City',
                'region_id' => $regions['VII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Southwestern University',
                'short_name' => 'SWU',
                'address' => 'Urgello Street, Cebu City',
                'region_id' => $regions['VII']->id ?? null,
                'status' => 'active',
            ],

            // REGION VIII (Eastern Visayas) - PRISAA Participating Schools
            [
                'name' => 'Visayas State University',
                'short_name' => 'VSU',
                'address' => 'Baybay, Leyte',
                'region_id' => $regions['VIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Eastern Visayas State University',
                'short_name' => 'EVSU',
                'address' => 'Tacloban City, Leyte',
                'region_id' => $regions['VIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Eastern Philippines',
                'short_name' => 'UEP',
                'address' => 'Catarman, Northern Samar',
                'region_id' => $regions['VIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Leyte Normal University',
                'short_name' => 'LNU',
                'address' => 'Tacloban City, Leyte',
                'region_id' => $regions['VIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Samar State University',
                'short_name' => 'SSU',
                'address' => 'Catbalogan, Samar',
                'region_id' => $regions['VIII']->id ?? null,
                'status' => 'active',
            ],

            // REGION IX (Zamboanga Peninsula) - PRISAA Participating Schools
            [
                'name' => 'Western Mindanao State University',
                'short_name' => 'WMSU',
                'address' => 'Normal Road, Baliwasan, Zamboanga City',
                'region_id' => $regions['IX']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Universidad de Zamboanga',
                'short_name' => 'UZ',
                'address' => 'La Purisima Street, Zamboanga City',
                'region_id' => $regions['IX']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Jose Rizal Memorial State University',
                'short_name' => 'JRMSU',
                'address' => 'Dapitan City, Zamboanga del Norte',
                'region_id' => $regions['IX']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Zamboanga State College of Marine Sciences and Technology',
                'short_name' => 'ZSCMST',
                'address' => 'Fort Pilar, Zamboanga City',
                'region_id' => $regions['IX']->id ?? null,
                'status' => 'active',
            ],

            // REGION X (Northern Mindanao) - PRISAA Participating Schools
            [
                'name' => 'Mindanao State University-Iligan Institute of Technology',
                'short_name' => 'MSU-IIT',
                'address' => 'Iligan City, Lanao del Norte',
                'region_id' => $regions['X']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Xavier University-Ateneo de Cagayan',
                'short_name' => 'XU',
                'address' => 'Corrales Avenue, Cagayan de Oro City',
                'region_id' => $regions['X']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Bukidnon State University',
                'short_name' => 'BSU',
                'address' => 'Malaybalay City, Bukidnon',
                'region_id' => $regions['X']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Central Mindanao University',
                'short_name' => 'CMU',
                'address' => 'Musuan, Bukidnon',
                'region_id' => $regions['X']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Capitol University',
                'short_name' => 'CU',
                'address' => 'Corrales Extension, Cagayan de Oro City',
                'region_id' => $regions['X']->id ?? null,
                'status' => 'active',
            ],

            // REGION XI (Davao Region) - PRISAA Participating Schools
            [
                'name' => 'University of Mindanao',
                'short_name' => 'UM',
                'address' => 'Bolton Street, Davao City',
                'region_id' => $regions['XI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Ateneo de Davao University',
                'short_name' => 'AdDU',
                'address' => 'E. Jacinto Street, Davao City',
                'region_id' => $regions['XI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of the Immaculate Conception',
                'short_name' => 'UIC',
                'address' => 'Fr. Selga Street, Davao City',
                'region_id' => $regions['XI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Davao del Norte State College',
                'short_name' => 'DNSC',
                'address' => 'Panabo City, Davao del Norte',
                'region_id' => $regions['XI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Southeastern Philippines',
                'short_name' => 'USEP',
                'address' => 'Bo. Obrero, Davao City',
                'region_id' => $regions['XI']->id ?? null,
                'status' => 'active',
            ],

            // REGION XII (SOCCSKSARGEN) - PRISAA Participating Schools
            [
                'name' => 'University of Southern Mindanao',
                'short_name' => 'USM',
                'address' => 'Kabacan, Cotabato',
                'region_id' => $regions['XII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Sultan Kudarat State University',
                'short_name' => 'SKSU',
                'address' => 'Tacurong City, Sultan Kudarat',
                'region_id' => $regions['XII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Cotabato State University',
                'short_name' => 'CSU',
                'address' => 'Cotabato City, Maguindanao',
                'region_id' => $regions['XII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Sarangani State University',
                'short_name' => 'SSU',
                'address' => 'Alabel, Sarangani',
                'region_id' => $regions['XII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'South Cotabato State College',
                'short_name' => 'SCSC',
                'address' => 'Surallah, South Cotabato',
                'region_id' => $regions['XII']->id ?? null,
                'status' => 'active',
            ],

            // NCR (National Capital Region) - PRISAA Participating Schools
            [
                'name' => 'University of the Philippines Diliman',
                'short_name' => 'UPD',
                'address' => 'University of the Philippines Diliman, Quezon City',
                'region_id' => $regions['NCR']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Ateneo de Manila University',
                'short_name' => 'ADMU',
                'address' => 'Katipunan Avenue, Loyola Heights, Quezon City',
                'region_id' => $regions['NCR']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'De La Salle University',
                'short_name' => 'DLSU',
                'address' => '2401 Taft Avenue, Manila',
                'region_id' => $regions['NCR']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Santo Tomas',
                'short_name' => 'UST',
                'address' => 'España Boulevard, Manila',
                'region_id' => $regions['NCR']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Far Eastern University',
                'short_name' => 'FEU',
                'address' => 'Nicanor Reyes Street, Sampaloc, Manila',
                'region_id' => $regions['NCR']->id ?? null,
                'status' => 'active',
            ],

            // CAR (Cordillera Administrative Region) - PRISAA Participating Schools
            [
                'name' => 'Benguet State University',
                'short_name' => 'BSU',
                'address' => 'La Trinidad, Benguet',
                'region_id' => $regions['CAR']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Saint Louis University',
                'short_name' => 'SLU',
                'address' => 'Bonifacio Street, Baguio City',
                'region_id' => $regions['CAR']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of the Cordilleras',
                'short_name' => 'UC',
                'address' => 'Governor Pack Road, Baguio City',
                'region_id' => $regions['CAR']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Kalinga State University',
                'short_name' => 'KSU',
                'address' => 'Tabuk, Kalinga',
                'region_id' => $regions['CAR']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Apayao State College',
                'short_name' => 'ASC',
                'address' => 'Luna, Apayao',
                'region_id' => $regions['CAR']->id ?? null,
                'status' => 'active',
            ],

            // CARAGA (Caraga Region) - PRISAA Participating Schools
            [
                'name' => 'Caraga State University',
                'short_name' => 'CSU',
                'address' => 'Ampayon, Butuan City',
                'region_id' => $regions['XIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Surigao del Sur State University',
                'short_name' => 'SDSSU',
                'address' => 'Tandag City, Surigao del Sur',
                'region_id' => $regions['XIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Agusan del Sur State College of Agriculture and Technology',
                'short_name' => 'ASSCAT',
                'address' => 'Trento, Agusan del Sur',
                'region_id' => $regions['XIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Surigao del Norte State University',
                'short_name' => 'SDNSU',
                'address' => 'Surigao City, Surigao del Norte',
                'region_id' => $regions['XIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Agusan del Norte College of Agriculture and Technology',
                'short_name' => 'ANCAT',
                'address' => 'Buenavista, Agusan del Norte',
                'region_id' => $regions['XIII']->id ?? null,
                'status' => 'active',
            ],
        ];

        foreach ($schools as $school) {
            School::create($school);
        }
    }
}