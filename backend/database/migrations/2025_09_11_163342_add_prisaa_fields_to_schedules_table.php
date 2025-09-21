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
        Schema::table('schedules', function (Blueprint $table) {
            // PRISAA Competition Structure
            $table->enum('competition_level', ['elementary', 'high_school', 'college'])->nullable()->after('priority');
            $table->enum('age_group', ['u12', 'u14', 'u16', 'u18', 'u21', 'open', 'masters'])->nullable()->after('competition_level');
            $table->enum('gender_category', ['mens', 'womens', 'mixed', 'co_ed'])->nullable()->after('age_group');
            $table->enum('educational_level', ['elementary', 'middle_school', 'high_school', 'college', 'professional'])->nullable()->after('gender_category');
            $table->string('sport_category')->nullable()->after('educational_level'); // weight class, division, specialty

            // Event Structure
            $table->enum('round_type', ['qualifying', 'preliminary', 'quarter_final', 'semi_final', 'final', 'bronze_final', 'gold_final', 'consolation'])->nullable()->after('sport_category');
            $table->integer('heat_number')->nullable()->after('round_type'); // for track events
            $table->integer('lane_number')->nullable()->after('heat_number'); // for track events
            $table->integer('court_field_number')->nullable()->after('lane_number'); // for team sports

            // Event Configuration
            $table->boolean('is_team_event')->default(true)->after('court_field_number');
            $table->integer('max_teams_per_school')->nullable()->after('is_team_event'); // PRISAA rule
            $table->json('qualification_criteria')->nullable()->after('max_teams_per_school');

            // Environmental Factors
            $table->string('weather_conditions')->nullable()->after('qualification_criteria');

            // Official Requirements
            $table->integer('technical_officials_required')->default(1)->after('weather_conditions');
            $table->integer('medical_officials_required')->default(1)->after('technical_officials_required');

            // Venue & Capacity
            $table->integer('spectator_capacity')->nullable()->after('medical_officials_required');

            // Broadcasting & Media
            $table->text('broadcast_info')->nullable()->after('spectator_capacity');
            $table->string('live_stream_url')->nullable()->after('broadcast_info');

            // Results & Scoring
            $table->enum('result_format', ['individual', 'team', 'relay', 'combined'])->nullable()->after('live_stream_url');
            $table->string('scoring_system_used')->nullable()->after('result_format');

            // Appeals & Protests
            $table->integer('protest_deadline_hours')->default(24)->after('scoring_system_used');
            $table->text('appeal_process_info')->nullable()->after('protest_deadline_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn([
                'competition_level',
                'age_group',
                'gender_category',
                'educational_level',
                'sport_category',
                'round_type',
                'heat_number',
                'lane_number',
                'court_field_number',
                'is_team_event',
                'max_teams_per_school',
                'qualification_criteria',
                'weather_conditions',
                'technical_officials_required',
                'medical_officials_required',
                'spectator_capacity',
                'broadcast_info',
                'live_stream_url',
                'result_format',
                'scoring_system_used',
                'protest_deadline_hours',
                'appeal_process_info'
            ]);
        });
    }
};
