<?php

namespace App\Services\Layer4;

use App\Models\Layer3\Result;
use App\Models\Layer3\ResultMetric;
use App\Models\Layer2\Athlete;

class ResultMetricsService
{
    public function addMetric(Result $result, Athlete $athlete, string $type, float|int $value): ResultMetric
    {
        return ResultMetric::updateOrCreate(
            [
                'result_id' => $result->id,
                'athlete_id' => $athlete->id,
                'stat_type' => $type,
            ],
            [
                'value' => $value
            ]
        );
    }

    public function getAthleteMetrics(Athlete $athlete)
    {
        return ResultMetric::where('athlete_id', $athlete->id)->get();
    }
}
