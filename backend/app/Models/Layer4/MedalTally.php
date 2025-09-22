<?php

namespace App\Models\Layer4;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer1\School;

class MedalTally extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'gold', 'silver', 'bronze', 'points'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
