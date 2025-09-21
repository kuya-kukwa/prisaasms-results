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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            
            // Basic school information
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->text('address')->nullable();
            $table->string('region')->nullable();
            $table->string('logo')->nullable();
            
            // School classification
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
