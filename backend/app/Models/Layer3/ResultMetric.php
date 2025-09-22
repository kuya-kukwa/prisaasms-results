<?php

namespace App\Models\Layer3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer3\Result;

class ResultMetric extends Model
{
    use HasFactory;

    protected $fillable = ['result_id', 'metric_name', 'metric_value'];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}
