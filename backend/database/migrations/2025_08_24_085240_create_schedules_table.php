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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            
            // Basic schedule information
            $table->string('title');
            $table->text('description')->nullable();
            
            // Timing
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            
            // Relationships - remove foreign key constraints for now
            $table->unsignedBigInteger('sport_id');
            $table->unsignedBigInteger('venue_id')->nullable();
            $table->unsignedBigInteger('tournament_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            // Event type
            $table->enum('event_type', ['match', 'practice', 'training', 'meeting', 'ceremony', 'other'])->default('match');
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled', 'postponed'])->default('scheduled');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Participants (could be teams, individuals, or groups)
            $table->json('participants')->nullable();
            $table->json('officials_assigned')->nullable();

            // System fields
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
