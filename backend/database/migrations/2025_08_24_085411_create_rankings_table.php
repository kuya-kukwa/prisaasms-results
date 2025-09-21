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
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            
            // Ranking context
            $table->string('ranking_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('entity_type');
            
            // Sport and competition context
            $table->unsignedBigInteger('sport_id');
            $table->unsignedBigInteger('tournament_id')->nullable();
            $table->string('season')->nullable();
            $table->enum('division', ['senior', 'junior', 'elementary', 'college', 'high_school'])->default('college');
            $table->enum('category', ['male', 'female', 'mixed'])->default('male');
            
            // Ranking details
            $table->integer('current_rank');
            $table->integer('previous_rank')->nullable();
            $table->integer('rank_change')->default(0);
            $table->decimal('points', 10, 2)->default(0);
            $table->decimal('rating', 8, 3)->nullable();
            
            // Performance metrics
            $table->integer('matches_played')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->integer('draws')->default(0);
            $table->decimal('win_percentage', 5, 2)->default(0);
            $table->integer('points_for')->default(0);
            $table->integer('points_against')->default(0);
            $table->decimal('point_differential', 10, 2)->default(0);
            
            // Additional metrics (sport-specific)
            $table->json('additional_stats')->nullable();
            
            // Ranking period
            $table->date('ranking_date');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            
            // Status
            $table->boolean('is_current')->default(true);
            $table->boolean('is_final')->default(false);
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['ranking_type', 'entity_id', 'sport_id']);
            $table->index(['current_rank', 'sport_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};
