<?php

namespace App\Models\Layer1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer1\Province;
use App\Models\Layer2\Athlete;
use App\Models\Layer2\Team;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'province_id'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
