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
        Schema::table('schools', function (Blueprint $table) {
            // Add region_id foreign key
            $table->foreignId('region_id')->nullable()->constrained('regions')->onDelete('set null');
            
            // Remove old region column
            $table->dropColumn('region');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Add back region column
            $table->string('region')->nullable();
            
            // Remove region_id foreign key
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });
    }
};
