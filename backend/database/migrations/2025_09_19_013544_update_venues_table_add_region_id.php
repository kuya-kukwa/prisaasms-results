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
        Schema::table('venues', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->constrained('regions')->onDelete('set null');
            $table->dropColumn('region');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->string('region')->nullable();
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });
    }
};
