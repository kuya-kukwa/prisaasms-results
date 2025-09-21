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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('birthdate')->nullable();
            $table->string('avatar')->nullable();

            // Relationships - remove foreign key constraints for now
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('sport_id')->nullable();

            // Athlete details
            $table->string('athlete_number')->unique();
            $table->enum('status', ['active', 'inactive', 'injured', 'suspended'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
