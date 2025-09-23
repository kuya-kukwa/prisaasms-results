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
                'name' => 'University of Luzon',
                'short_name' => 'UL',
                'address' => 'Dagupan City, Pangasinan',
                'region_id' => $regions['I']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Divine Word College of Vigan',
                'short_name' => 'DWCV',
                'address' => 'Vigan City, Ilocos Sur',
                'region_id' => $regions['I']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Lorma Colleges',
                'short_name' => 'LC',
                'address' => 'San Fernando City, La Union',
                'region_id' => $regions['I']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Saint Louis College of San Fernando',
                'short_name' => 'SLCSF',
                'address' => 'San Fernando City, La Union',
                'region_id' => $regions['I']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Colegio de Dagupan',
                'short_name' => 'CD',
                'address' => 'Dagupan City, Pangasinan',
                'region_id' => $regions['I']->id ?? null,
                'status' => 'active',
            ],

            // REGION II (Cagayan Valley) - PRISAA Participating Schools
            [
                'name' => 'Saint Paul University Philippines',
                'short_name' => 'SPUP',
                'address' => 'Tuguegarao City, Cagayan',
                'region_id' => $regions['II']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Saint Louis Tuguegarao',
                'short_name' => 'USLT',
                'address' => 'Tuguegarao City, Cagayan',
                'region_id' => $regions['II']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Lyceum of Aparri',
                'short_name' => 'LA',
                'address' => 'Aparri, Cagayan',
                'region_id' => $regions['II']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Santiago City Colleges',
                'short_name' => 'SCC',
                'address' => 'Santiago City, Isabela',
                'region_id' => $regions['II']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Northeastern College',
                'short_name' => 'NEC',
                'address' => 'Santiago City, Isabela',
                'region_id' => $regions['II']->id ?? null,
                'status' => 'active',
            ],

            // REGION III (Central Luzon) - PRISAA Participating Schools
            [
                'name' => 'Holy Angel University',
                'short_name' => 'HAU',
                'address' => 'Angeles City, Pampanga',
                'region_id' => $regions['III']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Angeles University Foundation',
                'short_name' => 'AUF',
                'address' => 'Angeles City, Pampanga',
                'region_id' => $regions['III']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of the Assumption',
                'short_name' => 'UA',
                'address' => 'San Fernando City, Pampanga',
                'region_id' => $regions['III']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Systems Plus College Foundation',
                'short_name' => 'SPCF',
                'address' => 'Angeles City, Pampanga',
                'region_id' => $regions['III']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'La Consolacion University Philippines',
                'short_name' => 'LCUP',
                'address' => 'Malolos, Bulacan',
                'region_id' => $regions['III']->id ?? null,
                'status' => 'active',
            ],

            // REGION IV-A (CALABARZON) - PRISAA Participating Schools
            [
                'name' => 'De La Salle University-Dasmariñas',
                'short_name' => 'DLSU-D',
                'address' => 'Dasmariñas, Cavite',
                'region_id' => $regions['IV-A']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Lyceum of the Philippines University - Batangas',
                'short_name' => 'LPU-B',
                'address' => 'Batangas City, Batangas',
                'region_id' => $regions['IV-A']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Perpetual Help System DALTA - Molino',
                'short_name' => 'UPHSD-M',
                'address' => 'Molino, Bacoor, Cavite',
                'region_id' => $regions['IV-A']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Emilio Aguinaldo College',
                'short_name' => 'EAC',
                'address' => 'Manila Cavite Road, Dasmariñas, Cavite',
                'region_id' => $regions['IV-A']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Colegio de San Juan de Letran',
                'short_name' => 'CSJL',
                'address' => 'Intramuros, Manila',
                'region_id' => $regions['IV-A']->id ?? null,
                'status' => 'active',
            ],

            // REGION V (Bicol Region) - PRISAA Participating Schools
            [
                'name' => 'Assumption College',
                'short_name' => 'ACC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Aquinas College Inc.',
                'short_name' => 'ACI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Albay College',
                'short_name' => 'ALB',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Bicol College',
                'short_name' => 'BC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Bicol Colleges Inc.',
                'short_name' => 'BCI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Bicol Medical Missionaries College Inc.',
                'short_name' => 'BMMCI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Catanduanes Technological College',
                'short_name' => 'CATC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Catanduanes Technological College Inc.',
                'short_name' => 'CATCI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Camarines Colleges Inc.',
                'short_name' => 'CCI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Camarines Norte College',
                'short_name' => 'CN',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Camarines Sur College',
                'short_name' => 'CS',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Divine Shepherd College',
                'short_name' => 'DSC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Eastern Samar Faith School',
                'short_name' => 'ESFS',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Lyceum',
                'short_name' => 'LYCEUM',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Mabini College',
                'short_name' => 'MABINI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Masbate Colleges Inc.',
                'short_name' => 'MCI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Masbate Provincial Colleges Foundation',
                'short_name' => 'MPCF',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Masbate Provincial Colleges Foundation - Legazpi City',
                'short_name' => 'MPCF-LC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Masbate Provincial Colleges Foundation LC',
                'short_name' => 'MPCFLC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Masbate Provincial Colleges Foundation LC',
                'short_name' => 'MPCLFC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Our Lady of Lourdes College Foundation',
                'short_name' => 'OLLCF',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Southern Colleges Inc.',
                'short_name' => 'SCCI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Southern Eastern Colleges of the North Inc.',
                'short_name' => 'SECNCI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Southern Institute of Technology',
                'short_name' => 'SIT',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'St. Louis De Marilac College of Sorsogon Inc.',
                'short_name' => 'SLMCSI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'St. Mary\'s College of Roxas',
                'short_name' => 'SMRC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'St. Mary\'s College of Roxas Inc.',
                'short_name' => 'SMRCI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'St. Raphael Medical Center College',
                'short_name' => 'SRMC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'STI College',
                'short_name' => 'STI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'St. Vincent College',
                'short_name' => 'SVC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Tabaco Learning Center',
                'short_name' => 'TLC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Tabaco School of Craftsmen',
                'short_name' => 'TSC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Santo Tomas',
                'short_name' => 'UST',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Santo Tomas - Legazpi',
                'short_name' => 'UST-L',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Santo Tomas Legazpi',
                'short_name' => 'USTL',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Virac Colleges Inc.',
                'short_name' => 'VCI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Virgen de las Gracias Institute',
                'short_name' => 'VGI',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Virgen de las Gracias Institute II',
                'short_name' => 'VGII',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Zamboanga Medical Missionaries College',
                'short_name' => 'ZMC',
                'address' => 'Bicol Region',
                'region_id' => $regions['V']->id ?? null,
                'status' => 'active',
            ],

            // REGION VI (Western Visayas) - PRISAA Participating Schools
            [
                'name' => 'Central Philippine University',
                'short_name' => 'CPU',
                'address' => 'Jaro, Iloilo City',
                'region_id' => $regions['VI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of San Agustin',
                'short_name' => 'USA',
                'address' => 'General Luna Street, Iloilo City',
                'region_id' => $regions['VI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Colegio de San Jose',
                'short_name' => 'CSJ',
                'address' => 'Jaro, Iloilo City',
                'region_id' => $regions['VI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'St. Paul University Iloilo',
                'short_name' => 'SPUI',
                'address' => 'General Luna Street, Iloilo City',
                'region_id' => $regions['VI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'John B. Lacson Foundation Maritime University',
                'short_name' => 'JBLFMU',
                'address' => 'Molo, Iloilo City',
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
                'name' => 'University of Cebu',
                'short_name' => 'UC',
                'address' => 'Cebu City, Cebu',
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
            [
                'name' => 'Cebu Institute of Technology - University',
                'short_name' => 'CIT-U',
                'address' => 'N. Bacalso Avenue, Cebu City',
                'region_id' => $regions['VII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of the Visayas',
                'short_name' => 'UV',
                'address' => 'Colon Street, Cebu City',
                'region_id' => $regions['VII']->id ?? null,
                'status' => 'active',
            ],

            // REGION VIII (Eastern Visayas) - PRISAA Participating Schools
            [
                'name' => 'University of Perpetual Help System DALTA - Tacloban',
                'short_name' => 'UPHSD-T',
                'address' => 'Tacloban City, Leyte',
                'region_id' => $regions['VIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Asian Development Foundation College',
                'short_name' => 'ADFC',
                'address' => 'Tacloban City, Leyte',
                'region_id' => $regions['VIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Divine Word University of Tacloban',
                'short_name' => 'DWUT',
                'address' => 'Tacloban City, Leyte',
                'region_id' => $regions['VIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Sacred Heart College',
                'short_name' => 'SHC',
                'address' => 'Catarman, Northern Samar',
                'region_id' => $regions['VIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'St. Paul University Philippines - Baybay',
                'short_name' => 'SPUP-B',
                'address' => 'Baybay City, Leyte',
                'region_id' => $regions['VIII']->id ?? null,
                'status' => 'active',
            ],

            // REGION IX (Zamboanga Peninsula) - PRISAA Participating Schools
            [
                'name' => 'Universidad de Zamboanga',
                'short_name' => 'UZ',
                'address' => 'La Purisima Street, Zamboanga City',
                'region_id' => $regions['IX']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Ateneo de Zamboanga University',
                'short_name' => 'ADZU',
                'address' => 'La Purisima Street, Zamboanga City',
                'region_id' => $regions['IX']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Western Mindanao State University - College of Teacher Education',
                'short_name' => 'WMSU-CTE',
                'address' => 'Zamboanga City',
                'region_id' => $regions['IX']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Southern City Colleges',
                'short_name' => 'SCC',
                'address' => 'Zamboanga City',
                'region_id' => $regions['IX']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Zamboanga Peninsula Polytechnic State University',
                'short_name' => 'ZPPSU',
                'address' => 'Dipolog City, Zamboanga del Norte',
                'region_id' => $regions['IX']->id ?? null,
                'status' => 'active',
            ],

            // REGION X (Northern Mindanao) - PRISAA Participating Schools
            [
                'name' => 'Xavier University-Ateneo de Cagayan',
                'short_name' => 'XU',
                'address' => 'Corrales Avenue, Cagayan de Oro City',
                'region_id' => $regions['X']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Liceo de Cagayan University',
                'short_name' => 'LDCU',
                'address' => 'Cagayan de Oro City',
                'region_id' => $regions['X']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'St. Paul University Philippines - Cagayan de Oro',
                'short_name' => 'SPUP-CDO',
                'address' => 'Cagayan de Oro City',
                'region_id' => $regions['X']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Science and Technology of Southern Philippines',
                'short_name' => 'USTP',
                'address' => 'Cagayan de Oro City',
                'region_id' => $regions['X']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Misamis University',
                'short_name' => 'MU',
                'address' => 'Ozamis City, Misamis Occidental',
                'region_id' => $regions['X']->id ?? null,
                'status' => 'active',
            ],

            // REGION XI (Davao Region) - PRISAA Participating Schools
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
                'name' => 'San Pedro College',
                'short_name' => 'SPC',
                'address' => 'Davao City',
                'region_id' => $regions['XI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Assumption College of Davao',
                'short_name' => 'ACD',
                'address' => 'Davao City',
                'region_id' => $regions['XI']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Holy Cross of Davao College',
                'short_name' => 'HCDC',
                'address' => 'Davao City',
                'region_id' => $regions['XI']->id ?? null,
                'status' => 'active',
            ],

            // REGION XII (SOCCSKSARGEN) - PRISAA Participating Schools
            [
                'name' => 'Notre Dame of Marbel University',
                'short_name' => 'NDMU',
                'address' => 'Korondal City, South Cotabato',
                'region_id' => $regions['XII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Holy Trinity College',
                'short_name' => 'HTC',
                'address' => 'General Santos City',
                'region_id' => $regions['XII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Marianne Children and Family Services Foundation',
                'short_name' => 'MCFSF',
                'address' => 'General Santos City',
                'region_id' => $regions['XII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'St. Anthony College',
                'short_name' => 'SAC',
                'address' => 'Tacurong City, Sultan Kudarat',
                'region_id' => $regions['XII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'STI College - General Santos',
                'short_name' => 'STI-GS',
                'address' => 'General Santos City',
                'region_id' => $regions['XII']->id ?? null,
                'status' => 'active',
            ],

            // NCR (National Capital Region) - PRISAA Participating Schools
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
            [
                'name' => 'Adamson University',
                'short_name' => 'AdU',
                'address' => '900 San Marcelino Street, Ermita, Manila',
                'region_id' => $regions['NCR']->id ?? null,
                'status' => 'active',
            ],

            // CAR (Cordillera Administrative Region) - PRISAA Participating Schools
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
                'name' => 'Baguio Central University',
                'short_name' => 'BCU',
                'address' => 'Baguio City',
                'region_id' => $regions['CAR']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'University of Baguio',
                'short_name' => 'UB',
                'address' => 'Baguio City',
                'region_id' => $regions['CAR']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Divine Word College of Bangued',
                'short_name' => 'DWCB',
                'address' => 'Bangued, Abra',
                'region_id' => $regions['CAR']->id ?? null,
                'status' => 'active',
            ],

            // CARAGA (Caraga Region) - PRISAA Participating Schools
            [
                'name' => 'St. Paul University Philippines - Surigao',
                'short_name' => 'SPUP-S',
                'address' => 'Surigao City, Surigao del Norte',
                'region_id' => $regions['XIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Surigao Education Center',
                'short_name' => 'SEC',
                'address' => 'Surigao City, Surigao del Norte',
                'region_id' => $regions['XIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Northwestern Agusan Colleges',
                'short_name' => 'NAC',
                'address' => 'Bayugan City, Agusan del Sur',
                'region_id' => $regions['XIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'St. Michael College of Caraga',
                'short_name' => 'SMCC',
                'address' => 'Nasipit, Agusan del Norte',
                'region_id' => $regions['XIII']->id ?? null,
                'status' => 'active',
            ],
            [
                'name' => 'Butuan Doctors College',
                'short_name' => 'BDC',
                'address' => 'Butuan City, Agusan del Norte',
                'region_id' => $regions['XIII']->id ?? null,
                'status' => 'active',
            ],
        ];

        foreach ($schools as $school) {
            School::create($school);
        }
    }
}