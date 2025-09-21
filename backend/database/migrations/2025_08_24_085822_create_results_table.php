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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            
            // Competition reference
            $table->unsignedBigInteger('match_id')->nullable();
            $table->unsignedBigInteger('tournament_id')->nullable();
            $table->unsignedBigInteger('sport_id');
            $table->string('event_name')->nullable();
            
            // Participant information
            $table->enum('participant_type', ['team', 'individual'])->default('team');
            $table->unsignedBigInteger('participant_id');
            $table->string('participant_name');
            $table->unsignedBigInteger('school_id')->nullable();

            // Result details
            $table->integer('position')->nullable();
            $table->enum('medal_type', ['gold', 'silver', 'bronze', 'none'])->default('none');
            $table->string('score')->nullable();
            
            // Competition context
            $table->enum('round_type', ['preliminary', 'semifinal', 'final'])->default('final');
            $table->enum('category', ['male', 'female', 'mixed'])->default('male');
            $table->date('competition_date');
            
            // Verification
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['participant_type', 'participant_id']);
            $table->index(['tournament_id', 'position']);
            $table->index(['sport_id', 'medal_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
