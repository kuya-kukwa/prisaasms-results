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
        Schema::create('medal_tallies', function (Blueprint $table) {
            $table->id();
            
            // Entity information (school, team, or individual)
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('entity_name');
            
            // Competition context
            $table->unsignedBigInteger('tournament_id')->nullable();
            $table->unsignedBigInteger('sport_id')->nullable();
            $table->string('season')->nullable();
            $table->enum('division', ['senior', 'junior', 'elementary', 'college', 'high_school'])->default('college');
            $table->enum('category', ['male', 'female', 'mixed', 'overall'])->default('overall');
            
            // Medal counts
            $table->integer('gold_medals')->default(0);
            $table->integer('silver_medals')->default(0);
            $table->integer('bronze_medals')->default(0);
            $table->integer('total_medals')->default(0);
            
            // Points system (if applicable)
            $table->decimal('gold_points', 8, 2)->default(0);
            $table->decimal('silver_points', 8, 2)->default(0);
            $table->decimal('bronze_points', 8, 2)->default(0);
            $table->decimal('total_points', 10, 2)->default(0);
            
            // Ranking information
            $table->integer('rank')->nullable();
            $table->integer('previous_rank')->nullable();
            
            // Breakdown by event/sport (for detailed tracking)
            $table->json('medal_breakdown')->nullable();
            $table->json('event_results')->nullable();
            
            // Tally period
            $table->date('tally_date');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            
            // Status
            $table->boolean('is_current')->default(true);
            $table->boolean('is_final')->default(false);
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['entity_type', 'entity_id']);
            $table->index(['tournament_id', 'rank']);
            $table->index(['total_medals', 'total_points']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medal_tallies');
    }
};
