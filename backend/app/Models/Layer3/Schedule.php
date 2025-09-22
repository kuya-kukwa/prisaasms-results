<?php

namespace App\Models\Layer3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer1\Tournament;
use App\Models\Layer1\Venue;
use App\Models\Layer3\Match;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['tournament_id', 'venue_id', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function matches()
    {
        return $this->hasMany(Match::class);
    }
}
