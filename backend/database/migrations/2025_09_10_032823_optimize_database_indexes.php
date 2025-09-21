<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes conditionally using raw SQL
        $indexes = [
            // Schools table
            "CREATE INDEX IF NOT EXISTS schools_region_index ON schools (region)",
            "CREATE INDEX IF NOT EXISTS schools_status_index ON schools (status)",
            "CREATE INDEX IF NOT EXISTS schools_region_status_index ON schools (region, status)",

            // Game matches table
            "CREATE INDEX IF NOT EXISTS game_matches_scheduled_start_index ON game_matches (scheduled_start)",
            "CREATE INDEX IF NOT EXISTS game_matches_status_index ON game_matches (status)",
            "CREATE INDEX IF NOT EXISTS game_matches_scheduled_status_index ON game_matches (scheduled_start, status)",
            "CREATE INDEX IF NOT EXISTS game_matches_sport_id_index ON game_matches (sport_id)",
            "CREATE INDEX IF NOT EXISTS game_matches_venue_id_index ON game_matches (venue_id)",
            "CREATE INDEX IF NOT EXISTS game_matches_tournament_id_index ON game_matches (tournament_id)",

            // Tournaments table
            "CREATE INDEX IF NOT EXISTS tournaments_status_index ON tournaments (status)",
            "CREATE INDEX IF NOT EXISTS tournaments_start_date_index ON tournaments (start_date)",
            "CREATE INDEX IF NOT EXISTS tournaments_end_date_index ON tournaments (end_date)",
            "CREATE INDEX IF NOT EXISTS tournaments_status_dates_index ON tournaments (status, start_date, end_date)",

            // Athletes table
            "CREATE INDEX IF NOT EXISTS athletes_school_id_index ON athletes (school_id)",
            "CREATE INDEX IF NOT EXISTS athletes_sport_id_index ON athletes (sport_id)",
            "CREATE INDEX IF NOT EXISTS athletes_status_index ON athletes (status)",

            // Teams table
            "CREATE INDEX IF NOT EXISTS teams_school_id_index ON teams (school_id)",
            "CREATE INDEX IF NOT EXISTS teams_sport_id_index ON teams (sport_id)",
            "CREATE INDEX IF NOT EXISTS teams_status_index ON teams (status)",

            // Results table
            "CREATE INDEX IF NOT EXISTS results_match_id_index ON results (match_id)",
            "CREATE INDEX IF NOT EXISTS results_participant_id_index ON results (participant_id)",
            "CREATE INDEX IF NOT EXISTS results_school_id_index ON results (school_id)",
            "CREATE INDEX IF NOT EXISTS results_created_at_index ON results (created_at)",
            "CREATE INDEX IF NOT EXISTS results_sport_medal_index ON results (sport_id, medal_type)",

            // Rankings table
            "CREATE INDEX IF NOT EXISTS rankings_sport_id_index ON rankings (sport_id)",
            "CREATE INDEX IF NOT EXISTS rankings_entity_id_index ON rankings (entity_id)",
            "CREATE INDEX IF NOT EXISTS rankings_tournament_id_index ON rankings (tournament_id)",
            "CREATE INDEX IF NOT EXISTS rankings_sport_rank_index ON rankings (sport_id, current_rank)",
            "CREATE INDEX IF NOT EXISTS rankings_type_entity_sport_index ON rankings (ranking_type, entity_id, sport_id)",
        ];

        foreach ($indexes as $indexSql) {
            try {
                DB::statement($indexSql);
            } catch (\Exception $e) {
                // Log but don't fail if index already exists
                Log::info("Index creation skipped: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        $dropIndexes = [
            "DROP INDEX IF EXISTS schools_region_index ON schools",
            "DROP INDEX IF EXISTS schools_status_index ON schools",
            "DROP INDEX IF EXISTS schools_region_status_index ON schools",
            "DROP INDEX IF EXISTS game_matches_scheduled_start_index ON game_matches",
            "DROP INDEX IF EXISTS game_matches_status_index ON game_matches",
            "DROP INDEX IF EXISTS game_matches_scheduled_status_index ON game_matches",
            "DROP INDEX IF EXISTS game_matches_sport_id_index ON game_matches",
            "DROP INDEX IF EXISTS game_matches_venue_id_index ON game_matches",
            "DROP INDEX IF EXISTS game_matches_tournament_id_index ON game_matches",
            "DROP INDEX IF EXISTS tournaments_status_index ON tournaments",
            "DROP INDEX IF EXISTS tournaments_start_date_index ON tournaments",
            "DROP INDEX IF EXISTS tournaments_end_date_index ON tournaments",
            "DROP INDEX IF EXISTS tournaments_status_dates_index ON tournaments",
            "DROP INDEX IF EXISTS athletes_school_id_index ON athletes",
            "DROP INDEX IF EXISTS athletes_sport_id_index ON athletes",
            "DROP INDEX IF EXISTS athletes_status_index ON athletes",
            "DROP INDEX IF EXISTS teams_school_id_index ON teams",
            "DROP INDEX IF EXISTS teams_sport_id_index ON teams",
            "DROP INDEX IF EXISTS teams_status_index ON teams",
            "DROP INDEX IF EXISTS results_match_id_index ON results",
            "DROP INDEX IF EXISTS results_participant_id_index ON results",
            "DROP INDEX IF EXISTS results_school_id_index ON results",
            "DROP INDEX IF EXISTS results_created_at_index ON results",
            "DROP INDEX IF EXISTS results_sport_medal_index ON results",
            "DROP INDEX IF EXISTS rankings_sport_id_index ON rankings",
            "DROP INDEX IF EXISTS rankings_entity_id_index ON rankings",
            "DROP INDEX IF EXISTS rankings_tournament_id_index ON rankings",
            "DROP INDEX IF EXISTS rankings_sport_rank_index ON rankings",
            "DROP INDEX IF EXISTS rankings_type_entity_sport_index ON rankings",
        ];

        foreach ($dropIndexes as $dropSql) {
            try {
                DB::statement($dropSql);
            } catch (\Exception $e) {
                // Log but don't fail
                Log::info("Index drop skipped: " . $e->getMessage());
            }
        }
    }
};