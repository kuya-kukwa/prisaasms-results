<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Medals (Gold, Silver, Bronze)
        Schema::create('medals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('athlete_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('tournament_id');
            $table->enum('medal_type', ['gold', 'silver', 'bronze']);
            $table->timestamps();
        });

        // Standings (for per-school / per-team rankings)
        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('tournament_id');
            $table->integer('gold_count')->default(0);
            $table->integer('silver_count')->default(0);
            $table->integer('bronze_count')->default(0);
            $table->integer('points')->default(0); // (Gold=5, Silver=3, Bronze=1)
            $table->timestamps();

            $table->unique(['school_id', 'tournament_id']);
        });

        // Player Awards (MVP, Best Scorer, etc.)
        Schema::create('player_awards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('athlete_id');
            $table->unsignedBigInteger('tournament_id')->nullable();
            $table->unsignedBigInteger('match_id')->nullable();
            $table->string('award_type'); // MVP, Best Scorer, Best Setter
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['athlete_id', 'tournament_id', 'award_type'], 'unique_player_award');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_awards');
        Schema::dropIfExists('standings');
        Schema::dropIfExists('medals');
    }
};
