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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            
            // Basic tournament information
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('tournament_code')->unique();
            $table->text('description')->nullable();
            
            // Tournament classification
            $table->enum('type', ['championship', 'invitational', 'league'])->default('championship');
            $table->enum('level', ['national', 'regional', 'provincial', 'local'])->default('regional');
            
            // Dates
            $table->date('start_date');
            $table->date('end_date');
            $table->date('registration_end')->nullable();
            
            // Organization
            $table->string('host_location');
            $table->unsignedBigInteger('host_school_id')->nullable();
            $table->unsignedBigInteger('tournament_manager_id')->nullable();
            
            // Tournament settings
            $table->boolean('has_medal_tally')->default(true);
            $table->json('sports_included')->nullable();
            
            // Status
            $table->enum('status', ['planning', 'registration_open', 'ongoing', 'completed', 'cancelled'])->default('planning');
            $table->boolean('is_public')->default(true);
            
            // Results
            $table->unsignedBigInteger('champion_school_id')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
