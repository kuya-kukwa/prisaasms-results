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
        Schema::create('sports', function (Blueprint $table) {
            $table->id();
            
            // Basic sport information
            $table->string('name');
            $table->text('description')->nullable();

            // Sport classification
            $table->enum('category', ['team_sport', 'individual_sport', 'combat_sport', 'track_field', 'swimming', 'ball_games', 'racket_sports', 'gymnastics', 'weightlifting', 'martial_arts', 'athletics', 'aquatics', 'cycling', 'other'])->default('team_sport');
            $table->enum('gender_category', ['male', 'female', 'mixed'])->default('mixed');
            
            // Game rules and settings
            $table->integer('max_players_per_team')->nullable();
            $table->integer('min_players_per_team')->nullable();
            $table->json('scoring_system')->nullable();
            $table->integer('game_duration_minutes')->nullable();

            // Tournament settings
            $table->enum('tournament_format', [
                'single_elimination', 
                'double_elimination', 
                'round_robin', 
                'swiss', 
                'league',
                'group_stage_knockout',
                'ladder',
                'time_based',
                'best_of_series',
                'pool_play'
            ])->default('single_elimination');
            $table->boolean('has_ranking_system')->default(true);
            
            // Status
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('icon')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sports');
    }
};
