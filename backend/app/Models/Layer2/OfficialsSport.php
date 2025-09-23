<?php

namespace App\Models\Layer2;

use App\Models\Layer1\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Layer2\Sport;
use Illuminate\Database\Eloquent\SoftDeletes;   
class OfficialSport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'officials_sport';

    protected $fillable = [
        'official_id',
        'sport_id',
    ];

    // -------------------------------
    // Relationships
    // -------------------------------
    public function official()
    {
        return $this->belongsTo(User::class, 'official_id');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }
}
