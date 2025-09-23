<?php

namespace App\Services\Layer3;

use App\Models\Layer3\Schedule;
use App\Models\Layer3\GameMatch;

class ScheduleStatisticsService
{
    public function getScheduleStats(Schedule $schedule): array
    {
        $matches = $schedule->matches;
        $completed = $matches->where('status', 'completed')->count();
        $ongoing = $matches->where('status', 'ongoing')->count();
        $pending = $matches->where('status', 'pending')->count();

        return [
            'total_matches' => $matches->count(),
            'completed' => $completed,
            'ongoing' => $ongoing,
            'pending' => $pending,
        ];
    }
}
