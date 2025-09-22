<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Regions
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable();
            $table->timestamps();
        });

        // Provinces
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('schools', function (Blueprint $table) {
            $table->id(); // this creates BIGINT UNSIGNED by default
            $table->string('name');
            $table->unsignedBigInteger('province_id');
            $table->timestamps();

            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');
        });


        // Divisions (Boys, Girls, Men, Women, Mixed Youth, etc.)
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Season Years
        Schema::create('season_years', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });


        // Users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('contact_number')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('role', ['admin', 'coach', 'tournament_manager', 'official'])->default('coach');
            $table->string('avatar')->nullable();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('role');
        });

        // Officials (Referees, Umpires, etc.)
        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('position')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officials');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sports');
        Schema::dropIfExists('season_years');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('regions');
    }
};
