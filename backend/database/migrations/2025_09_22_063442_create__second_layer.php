<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -------------------------
        // Sports
        // -------------------------
        Schema::create('sports', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Basketball, Chess, Archery
            $table->enum('type', ['individual', 'team']);
            $table->enum('result_format', ['score', 'time', 'distance', 'points', 'set_based'])
                ->default('score');
            $table->text('description')->nullable(); // ✅ added
            $table->timestamps();
            $table->softDeletes();
        });


        // -------------------------
        // Sport Subcategories
        // -------------------------
        Schema::create('sport_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "3x3", "5x5", "30m", "100m Dash"
            $table->string('gender')->nullable(); // male, female, mixed
            $table->unsignedBigInteger('division_id')->nullable()->index();
            $table->decimal('min_weight', 5,2)->nullable();
            $table->decimal('max_weight', 5,2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        // -------------------------
        // Weight Classes   
        // -------------------------
        Schema::create('weight_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained()->cascadeOnDelete(); // belongs to a sport
            $table->string('name'); // e.g., Flyweight, Heavyweight
            $table->decimal('min_weight', 5, 2)->nullable();
            $table->decimal('max_weight', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        // -------------------------
        // Teams (for team sports)
        // -------------------------
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sport_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable(); // e.g. "Team A"
            $table->timestamps();
            $table->softDeletes();
        });


        // -------------------------
        // Athletes
        // -------------------------
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('weight_class_id')->nullable()->constrained('weight_classes')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('birthdate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('athlete_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // -------------------------
        // Athlete-Sport-Subcategory pivot
        // -------------------------
        Schema::create('athlete_sport_subcategory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sport_subcategory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('officials', function (Blueprint $table) {
            $table->id(); // unsignedBigInteger primary key
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // link to users
            $table->string('position')->nullable();
            $table->timestamps();
        });



        // -------------------------
        // Officials-Sport pivot
        // -------------------------
        Schema::create('officials_sport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('official_id')->constrained('officials')->cascadeOnDelete();
            $table->foreignId('sport_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('officials_sport_subcategory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('official_id')->constrained('officials')->cascadeOnDelete();
            $table->foreignId('sport_subcategory_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('officials_weight_class', function (Blueprint $table) {
            $table->id();
            $table->foreignId('official_id')->constrained('officials')->cascadeOnDelete();
            $table->foreignId('weight_class_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });



    }

    public function down(): void
    {
        Schema::dropIfExists('officials_sport');
        Schema::dropIfExists('officials');
        Schema::dropIfExists('athlete_sport_subcategory');
        Schema::dropIfExists('athletes');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('weight_classes');
        Schema::dropIfExists('sport_subcategories');
        Schema::dropIfExists('sports');
    }   
};
