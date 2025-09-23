<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ----------------------------
        // Regions
        // ----------------------------
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ----------------------------
        // Provinces
        // ----------------------------
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

        });

        // ----------------------------
        // Schools
        // ----------------------------
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

        });

        // ----------------------------
        // Divisions (Boys, Girls, Men, Women, Mixed, etc.)
        // ----------------------------
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // ----------------------------
        // Season Years
        // ----------------------------
        Schema::create('season_years', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->boolean('active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // ----------------------------
        // Tournaments
        // ----------------------------
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('level', ['school', 'provincial', 'regional', 'national']);
            $table->foreignId('season_year_id')->constrained()->cascadeOnDelete();

            // ✅ Host info
            $table->foreignId('host_school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->foreignId('host_province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('host_region_id')->nullable()->constrained('regions')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // ----------------------------
        // Venues
        // ----------------------------
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ----------------------------
        // Users
        // ----------------------------
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('contact_number')->nullable();
            $table->timestamp('email_verified_at')->nullable();

            $table->enum('role', [
                'admin',
                'coach',
                'tournament_manager',
            ])->default('coach');

            $table->string('avatar')->nullable();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });


        // ----------------------------
        // Pivot: School ↔ Tournament
        // ----------------------------
        Schema::create('school_tournament', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'tournament_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_tournament');
        Schema::dropIfExists('users');
        Schema::dropIfExists('venues');
        Schema::dropIfExists('tournaments');
        Schema::dropIfExists('season_years');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('schools');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('regions');
    }
};
