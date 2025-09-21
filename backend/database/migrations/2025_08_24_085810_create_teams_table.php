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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            
            // Basic team information
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('team_code')->unique()->nullable();
            
            // Relationships
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('sport_id');
            $table->unsignedBigInteger('coach_id')->nullable();
            
            // Team classification
            $table->enum('gender_category', ['male', 'female', 'mixed'])->default('male');
            $table->enum('division', ['senior', 'junior', 'elementary', 'college', 'high_school'])->default('college');
            
            // Team details
            $table->year('season_year');
            $table->string('uniform_color_primary')->nullable();
            $table->string('uniform_color_secondary')->nullable();
            $table->string('team_logo')->nullable();
            $table->text('team_motto')->nullable();
            
            // Performance tracking
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->integer('draws')->default(0);
            $table->decimal('win_percentage', 5, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'disbanded', 'suspended'])->default('active');
            
            // Contact and additional info
            $table->string('contact_person')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
