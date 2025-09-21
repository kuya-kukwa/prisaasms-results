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
        Schema::create('game_matches', function (Blueprint $table) {
            $table->id();
            
            // Basic match information
            $table->string('match_code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            
            // Relationships
            $table->unsignedBigInteger('sport_id');
            $table->unsignedBigInteger('venue_id')->nullable();
            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->unsignedBigInteger('tournament_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            // Teams/Participants
            $table->unsignedBigInteger('team_a_id')->nullable();
            $table->unsignedBigInteger('team_b_id')->nullable();
            $table->json('participants')->nullable();
            
            // Match timing
            $table->datetime('scheduled_start');
            $table->datetime('actual_start')->nullable();
            $table->datetime('actual_end')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->time('halftime_duration')->nullable();
            $table->integer('overtime_minutes')->default(0);
            
            // Match details
            $table->enum('match_type', ['regular', 'playoff', 'semifinal', 'final', 'exhibition', 'friendly'])->default('regular');
            $table->enum('status', ['scheduled', 'ongoing', 'halftime', 'overtime', 'completed', 'cancelled', 'postponed', 'forfeit'])->default('scheduled');
            $table->string('round')->nullable();
            $table->integer('match_number')->nullable();
            
            // Officials
            $table->json('officials_assigned')->nullable();
            $table->unsignedBigInteger('head_referee_id')->nullable();

            // Scoring and results
            $table->json('score_team_a')->nullable();
            $table->json('score_team_b')->nullable();
            $table->integer('final_score_team_a')->nullable();
            $table->integer('final_score_team_b')->nullable();
            $table->unsignedBigInteger('winner_id')->nullable();
            $table->enum('result_type', ['win', 'loss', 'draw', 'forfeit', 'no_contest'])->nullable();
            $table->boolean('is_upset')->default(false);
            
            // Match statistics
            $table->json('match_statistics')->nullable();
            $table->json('penalties')->nullable();
            $table->json('timeouts_used')->nullable();
            $table->text('match_notes')->nullable();
            
            // Match validation
            $table->boolean('result_confirmed')->default(false);
            $table->timestamp('result_confirmed_at')->nullable();
            $table->unsignedBigInteger('result_confirmed_by')->nullable();
            $table->boolean('protest_filed')->default(false);
            $table->text('protest_details')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_matches');
    }
};
