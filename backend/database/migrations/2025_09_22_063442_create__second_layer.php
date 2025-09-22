<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sports table
        Schema::create('sports', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Basketball, Chess, Archery
            $table->enum('type', ['individual', 'team']); // Individual vs Team sports
            $table->enum('result_format', ['score', 'time', 'distance', 'points', 'set_based'])
                  ->default('score'); 
            // score (basketball), time (athletics), distance (long jump), points (chess), set_based (volleyball, tennis)
            $table->timestamps();
        });

        // Sport subcategories
        Schema::create('sport_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "3x3", "5x5", "30m", "100m Dash"
            $table->string('gender')->nullable(); // male, female, mixed
            $table->unsignedBigInteger('division_id')->nullable()->index(); // link to division (boys/girls/men/women)
            $table->decimal('min_weight', 5,2)->nullable(); // for combat sports
            $table->decimal('max_weight', 5,2)->nullable();
            $table->timestamps();
        });

        // Teams (for team sports)
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sport_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable(); // optional (e.g. "Team A")
            $table->timestamps();
        });

        // Athletes
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender');
            $table->date('birthdate')->nullable();
            $table->timestamps();
        });

        // Athlete-Sport-Subcategory pivot
        Schema::create('athlete_sport_subcategory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sport_subcategory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('athlete_sport_subcategory');
        Schema::dropIfExists('athletes');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('sport_subcategories');
        Schema::dropIfExists('sports');
    }
};
