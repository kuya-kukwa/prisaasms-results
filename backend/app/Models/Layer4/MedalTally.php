<?php

namespace App\Models\Layer4;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer1\School;

class MedalTally extends Model
{
    use HasFactory;

    protected $table = 'standings'; // since migration used "standings"

    protected $fillable = [
        'school_id',
        'tournament_id',
        'gold_count',
        'silver_count',
        'bronze_count',
        'points',
    ];

    public function school()
    {
        return $this->belongsTo(\App\Models\Layer1\School::class);
    }

    public function tournament()
    {
        return $this->belongsTo(\App\Models\Layer1\Tournament::class);
    }
}
