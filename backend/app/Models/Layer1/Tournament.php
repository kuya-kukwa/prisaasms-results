<?php

namespace App\Models\Layer1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tournament extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'level',
        'season_year_id',
        'host_school_id',
        'host_province_id',
        'host_region_id',
    ];

    public function seasonYear()
    {
        return $this->belongsTo(SeasonYear::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_tournament');
    }

    public function hostSchool()
    {
        return $this->belongsTo(School::class, 'host_school_id');
    }

    public function hostProvince()
    {
        return $this->belongsTo(Province::class, 'host_province_id');
    }

    public function hostRegion()
    {
        return $this->belongsTo(Region::class, 'host_region_id');
    }
}


