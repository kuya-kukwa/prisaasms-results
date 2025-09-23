<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ------------------------
        // Schedules
        // ------------------------
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id')->index();
            $table->unsignedBigInteger('division_id')->index();
            $table->unsignedBigInteger('sport_id')->index();
            $table->unsignedBigInteger('sport_subcategory_id')->nullable()->index();
            $table->unsignedBigInteger('venue_id')->nullable()->index();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
            // Soft delete optional
            $table->softDeletes();
        });

        // ------------------------
        // Matches
        // ------------------------
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id')->index();
            $table->unsignedBigInteger('team_a_id')->nullable()->index();
            $table->unsignedBigInteger('team_b_id')->nullable()->index();
            $table->unsignedBigInteger('winner_id')->nullable()->index();
            $table->enum('status', ['pending', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('scheduled_at');
            $table->timestamps();
            $table->softDeletes();
        });

        // ------------------------
        // Results
        // ------------------------
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('athlete_id')->nullable()->index();
            $table->integer('score')->default(0);
            $table->enum('outcome', ['win', 'loss', 'draw'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ------------------------
        // Result Metrics
        // ------------------------
        Schema::create('result_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('result_id')->index();
            $table->unsignedBigInteger('athlete_id')->nullable()->index();
            $table->string('stat_type'); // e.g., points, rebounds
            $table->decimal('value', 8, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // ------------------------
        // Match Officials
        // ------------------------
        Schema::create('match_officials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('role')->default('referee');
            $table->timestamps();
            $table->softDeletes();
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
