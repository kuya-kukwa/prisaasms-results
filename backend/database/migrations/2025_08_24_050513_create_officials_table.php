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
        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            
            // Basic personal information
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('birthdate')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            
            // Official credentials
            $table->enum('certification_level', ['national', 'regional', 'local', 'trainee'])->default('local');
            
            // Official role and specialization
            $table->enum('official_type', ['referee', 'umpire', 'judge', 'timekeeper', 'scorer', 'technical_official', 'line_judge', 'table_official', 'starter', 'field_judge', 'track_judge', 'swimming_judge', 'diving_judge', 'gymnastics_judge', 'athletics_official', 'team_manager', 'match_commissioner', 'protest_jury_member'])->default('referee');
            $table->json('sports_certified')->nullable();
            $table->integer('years_experience')->default(0);
            
            // Assignment and availability
            $table->enum('status', ['active', 'inactive', 'suspended', 'retired'])->default('active');
            $table->boolean('available_for_assignment')->default(true);
            $table->json('availability_schedule')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officials');
    }
};
