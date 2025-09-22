<?php

namespace App\Models\Layer1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer1\Province;
use App\Models\Layer1\School;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

    public function schools()
    {
        return $this->hasMany(School::class);
    }
}
