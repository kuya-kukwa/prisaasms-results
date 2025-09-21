<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = [
            // Team Sports
            [
                'name' => 'Basketball',
                'description' => 'A fast-paced team sport played on a rectangular court',
                'category' => 'team_sport',
                'gender_category' => 'mixed',
                'max_players_per_team' => 12,
                'min_players_per_team' => 5,
                'game_duration_minutes' => 40,
                'tournament_format' => 'single_elimination',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '🏀'
            ],
            [
                'name' => 'Football (Soccer)',
                'description' => 'The world\'s most popular sport played between two teams',
                'category' => 'team_sport',
                'gender_category' => 'mixed',
                'max_players_per_team' => 22,
                'min_players_per_team' => 11,
                'game_duration_minutes' => 90,
                'tournament_format' => 'single_elimination',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '⚽'
            ],
            [
                'name' => 'Volleyball',
                'description' => 'A team sport in which two teams compete to score points by grounding a ball',
                'category' => 'team_sport',
                'gender_category' => 'mixed',
                'max_players_per_team' => 12,
                'min_players_per_team' => 6,
                'game_duration_minutes' => 25,
                'tournament_format' => 'round_robin',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '🏐'
            ],
            [
                'name' => 'Handball',
                'description' => 'A team sport where two teams pass a ball using their hands',
                'category' => 'team_sport',
                'gender_category' => 'mixed',
                'max_players_per_team' => 16,
                'min_players_per_team' => 7,
                'game_duration_minutes' => 30,
                'tournament_format' => 'single_elimination',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '🤾'
            ],

            // Individual Sports
            [
                'name' => 'Athletics (Track and Field)',
                'description' => 'Competitive sports involving running, jumping, and throwing',
                'category' => 'track_field',
                'gender_category' => 'mixed',
                'max_players_per_team' => null,
                'min_players_per_team' => null,
                'game_duration_minutes' => null,
                'tournament_format' => 'single_elimination',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '🏃'
            ],
            [
                'name' => 'Swimming',
                'description' => 'Individual or team sport that involves using arms and legs to move through water',
                'category' => 'swimming',
                'gender_category' => 'mixed',
                'max_players_per_team' => null,
                'min_players_per_team' => null,
                'game_duration_minutes' => null,
                'tournament_format' => 'single_elimination',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '🏊'
            ],
            [
                'name' => 'Table Tennis',
                'description' => 'A sport in which two or four players hit a lightweight ball back and forth',
                'category' => 'racket_sports',
                'gender_category' => 'mixed',
                'max_players_per_team' => null,
                'min_players_per_team' => null,
                'game_duration_minutes' => null,
                'tournament_format' => 'single_elimination',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '🏓'
            ],
            [
                'name' => 'Badminton',
                'description' => 'A racquet sport played using racquets to hit a shuttlecock across a net',
                'category' => 'racket_sports',
                'gender_category' => 'mixed',
                'max_players_per_team' => null,
                'min_players_per_team' => null,
                'game_duration_minutes' => null,
                'tournament_format' => 'single_elimination',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '🏸'
            ],

            // Combat Sports
            [
                'name' => 'Karate',
                'description' => 'A martial art developed in the Ryukyu Kingdom',
                'category' => 'martial_arts',
                'gender_category' => 'mixed',
                'max_players_per_team' => null,
                'min_players_per_team' => null,
                'game_duration_minutes' => null,
                'tournament_format' => 'single_elimination',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '🥋'
            ],
            [
                'name' => 'Taekwondo',
                'description' => 'A Korean martial art characterized by its emphasis on head-height kicks',
                'category' => 'martial_arts',
                'gender_category' => 'mixed',
                'max_players_per_team' => null,
                'min_players_per_team' => null,
                'game_duration_minutes' => null,
                'tournament_format' => 'single_elimination',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '🥋'
            ],

            // Gymnastics
            [
                'name' => 'Artistic Gymnastics',
                'description' => 'A discipline of gymnastics in which athletes perform artistic routines',
                'category' => 'gymnastics',
                'gender_category' => 'mixed',
                'max_players_per_team' => null,
                'min_players_per_team' => null,
                'game_duration_minutes' => null,
                'tournament_format' => 'single_elimination',
                'has_ranking_system' => true,
                'status' => 'active',
                'icon' => '🤸'
            ]
        ];

        foreach ($sports as $sport) {
            Sport::create($sport);
        }
    }
}
