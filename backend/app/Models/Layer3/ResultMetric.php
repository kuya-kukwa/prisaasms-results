<?php

namespace App\Models\Layer3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer3\Result;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResultMetric extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['result_id', 'athlete_id', 'stat_type', 'value'];

    public function result() { return $this->belongsTo(Result::class); }
}

