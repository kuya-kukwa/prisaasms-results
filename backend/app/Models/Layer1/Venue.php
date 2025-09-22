<?php

namespace App\Models\Layer1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer1\School;
use App\Models\Layer1\Province;
use App\Models\Layer1\Region;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'school_id', 'province_id', 'region_id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
