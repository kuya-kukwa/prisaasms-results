<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Schedules
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('division_id');
            $table->unsignedBigInteger('sport_id');
            $table->unsignedBigInteger('sport_subcategory_id')->nullable();
            $table->unsignedBigInteger('venue_id')->nullable();
            $table->dateTime('scheduled_at');
            $table->timestamps();
        });

        // Matches
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('team_a_id')->nullable();
            $table->unsignedBigInteger('team_b_id')->nullable();
            $table->unsignedBigInteger('winner_id')->nullable();
            $table->enum('status', ['pending', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // Results (overall per match)
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('athlete_id')->nullable(); // for individual sports
            $table->integer('score')->default(0);
            $table->string('outcome')->nullable(); // win, loss, draw, etc.
            $table->timestamps();
        });

        // Result Metrics (detailed stats per athlete)
        Schema::create('result_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('result_id');
            $table->unsignedBigInteger('athlete_id')->nullable();
            $table->string('stat_type'); // e.g., points, rebounds, assists, goals, attempts
            $table->decimal('value', 8, 2)->default(0);
            $table->timestamps();
        });

        // Match Officials
        Schema::create('match_officials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('user_id'); // referee / umpire
            $table->string('role')->default('referee'); // referee, umpire, linesman
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_officials');
        Schema::dropIfExists('result_metrics');
        Schema::dropIfExists('results');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('schedules');
    }
};
