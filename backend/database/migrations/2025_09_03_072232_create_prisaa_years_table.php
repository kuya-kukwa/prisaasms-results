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
        Schema::create('prisaa_years', function (Blueprint $table) {
            $table->id();
            
            // Basic year information
            $table->year('year')->unique();
            
            // Host information
            $table->string('host_region')->nullable();
            $table->string('host_province')->nullable();
            $table->string('host_city')->nullable();
            
            // Dates
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Statistics
            $table->integer('total_participants')->default(0);
            $table->integer('total_schools')->default(0);
            $table->integer('total_sports')->default(0);
            $table->integer('total_events')->default(0);
            
            // Management
            $table->enum('status', ['planning', 'ongoing', 'completed', 'cancelled'])->default('planning');
            $table->unsignedBigInteger('director_id')->nullable();
            
            // Additional information
            $table->text('description')->nullable();
            $table->json('highlights')->nullable();
            $table->json('achievements')->nullable();
            $table->json('records_broken')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('year');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prisaa_years');
    }
};
