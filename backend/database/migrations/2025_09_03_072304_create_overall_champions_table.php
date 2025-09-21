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
        Schema::create('overall_champions', function (Blueprint $table) {
            $table->id();
            
            // References
            $table->unsignedBigInteger('prisaa_year_id');
            $table->enum('level', ['provincial', 'regional', 'national']);
            $table->string('category')->nullable(); // e.g., 'elementary', 'secondary', 'overall'
            $table->unsignedBigInteger('school_id');
            
            // Performance metrics
            $table->decimal('points', 10, 2)->default(0);
            $table->integer('gold_medals')->default(0);
            $table->integer('silver_medals')->default(0);
            $table->integer('bronze_medals')->default(0);
            $table->integer('total_medals')->default(0);
            $table->integer('rank');
            
            // Location context
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['prisaa_year_id', 'level']);
            $table->index(['school_id', 'prisaa_year_id']);
            $table->index('rank');
            
            // Unique constraint to prevent duplicate entries (shortened name)
            $table->unique(['prisaa_year_id', 'level', 'category', 'school_id', 'rank'], 'oc_unique_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overall_champions');
    }
};
