<?php

namespace App\Services;

use App\Models\Layer1\Tournament;
use App\Models\Layer1\School;
use App\Models\Layer4\MedalTally;
use Illuminate\Support\Collection;

class TournamentChampionService
{
    /**
     * Get the overall champion for a given tournament level and season year.
     */
    public function getOverallChampion(
        string $level, 
        int $seasonYearId, 
        ?int $provinceId = null, 
        ?int $regionId = null
    ): ?array {
        $rankings = $this->getRankingTable($level, $seasonYearId, $provinceId, $regionId);
        return $rankings->first() ?? null;
    }

    /**
     * Get the full ranking table for a given level and season year.
     *
     * @param string $level 'provincial', 'regional', 'national'
     * @param int $seasonYearId
     * @param int|null $provinceId
     * @param int|null $regionId
     */
    public function getRankingTable(
        string $level, 
        int $seasonYearId, 
        ?int $provinceId = null, 
        ?int $regionId = null
    ): Collection {
        // Step 1: Get relevant tournaments
        $query = Tournament::where('level', $level)
            ->where('season_year_id', $seasonYearId);

        if ($level === 'provincial' && $provinceId) {
            $query->where('host_province_id', $provinceId);
        }

        if ($level === 'regional' && $regionId) {
            $query->where('host_region_id', $regionId);
        }

        $tournaments = $query->get();

        if ($tournaments->isEmpty()) {
            return collect();
        }

        // Step 2: Aggregate medal tallies per school
        $tallies = [];

        foreach ($tournaments as $tournament) {
            $medals = MedalTally::where('tournament_id', $tournament->id)->get();

            foreach ($medals as $medal) {
                $schoolId = $medal->school_id;
                if (!isset($tallies[$schoolId])) {
                    $tallies[$schoolId] = [
                        'school_id' => $schoolId,
                        'gold_count' => 0,
                        'silver_count' => 0,
                        'bronze_count' => 0,
                        'points' => 0,
                    ];
                }

                $tallies[$schoolId]['gold_count'] += $medal->gold_count;
                $tallies[$schoolId]['silver_count'] += $medal->silver_count;
                $tallies[$schoolId]['bronze_count'] += $medal->bronze_count;
                $tallies[$schoolId]['points'] += $medal->points;
            }
        }

        // Step 3: Convert to collection and sort
        $sorted = collect($tallies)->sortByDesc(function ($item) {
            return [$item['points'], $item['gold_count'], $item['silver_count'], $item['bronze_count']];
        })->values();

        // Step 4: Load school names in batch to avoid N+1 queries
        $schoolIds = $sorted->pluck('school_id')->all();
        $schools = School::whereIn('id', $schoolIds)->get()->keyBy('id');

        // Step 5: Attach school names
        $sorted = $sorted->map(function ($item) use ($schools) {
            return [
                'school_id' => $item['school_id'],
                'school' => $schools[$item['school_id']]->name ?? 'Unknown',
                'points' => $item['points'],
                'gold' => $item['gold_count'],
                'silver' => $item['silver_count'],
                'bronze' => $item['bronze_count'],
            ];
        });

        return $sorted;
    }

    /**
     * Get champion for a given level, including hierarchical constraints.
     * Example: only gold medal winners advance to higher-level tournaments.
     */
    public function getHierarchicalChampion(
        string $level, 
        int $seasonYearId, 
        ?int $provinceId = null, 
        ?int $regionId = null
    ): ?array {
        $ranking = $this->getRankingTable($level, $seasonYearId, $provinceId, $regionId);

        if ($ranking->isEmpty()) {
            return null;
        }

        // You can add extra conditions here, e.g. require at least 1 gold medal
        $champion = $ranking->first();
        if ($champion['gold'] === 0) {
            return null;
        }

        return $champion;
    }
}
