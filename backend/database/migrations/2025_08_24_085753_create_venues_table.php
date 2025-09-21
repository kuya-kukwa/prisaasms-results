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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            
            // Basic venue information
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('venue_code')->unique()->nullable();
            $table->text('address');
            $table->string('region')->nullable();
            $table->unsignedBigInteger('host_school_id')->nullable();
            
            // Contact information
            $table->string('contact_person')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            
            // Venue specifications
            $table->enum('venue_type', ['indoor', 'outdoor', 'covered'])->default('indoor');
            $table->enum('venue_category', ['gymnasium', 'field', 'court', 'track', 'pool', 'stadium'])->default('gymnasium');
            $table->enum('status', ['active', 'inactive', 'under_maintenance'])->default('active');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['venue_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
