<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add foreign keys to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
        });

        // Add foreign keys to athletes table
        Schema::table('athletes', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('set null');
        });

        // Add foreign keys to schedules table
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('set null');
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Add foreign keys to game_matches table
        Schema::table('game_matches', function (Blueprint $table) {
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('set null');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('set null');
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
            $table->foreign('team_a_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('team_b_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('winner_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('head_referee_id')->references('id')->on('officials')->onDelete('set null');
            $table->foreign('result_confirmed_by')->references('id')->on('users')->onDelete('set null');
        });

        // Add foreign keys to venues table
        Schema::table('venues', function (Blueprint $table) {
            $table->foreign('host_school_id')->references('id')->on('schools')->onDelete('set null');
        });

        // Add foreign keys to teams table
        Schema::table('teams', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            $table->foreign('coach_id')->references('id')->on('athletes')->onDelete('set null');
        });

        // Add foreign keys to tournaments table
        Schema::table('tournaments', function (Blueprint $table) {
            $table->foreign('host_school_id')->references('id')->on('schools')->onDelete('set null');
            $table->foreign('tournament_manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('champion_school_id')->references('id')->on('schools')->onDelete('set null');
        });

        // Add foreign keys to rankings table
        Schema::table('rankings', function (Blueprint $table) {
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('set null');
        });

        // Add foreign keys to medal_tallies table
        Schema::table('medal_tallies', function (Blueprint $table) {
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('set null');
        });

        // Add foreign keys to results table
        Schema::table('results', function (Blueprint $table) {
            $table->foreign('match_id')->references('id')->on('game_matches')->onDelete('cascade');
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys in reverse order
        Schema::table('game_matches', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropForeign(['venue_id']);
            $table->dropForeign(['schedule_id']);
            $table->dropForeign(['tournament_id']);
            $table->dropForeign(['team_a_id']);
            $table->dropForeign(['team_b_id']);
            $table->dropForeign(['winner_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['head_referee_id']);
            $table->dropForeign(['result_confirmed_by']);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropForeign(['venue_id']);
            $table->dropForeign(['tournament_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::table('athletes', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['sport_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
        });
    }
};
