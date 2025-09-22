<?php

namespace App\Models\Layer1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer2\Athlete;
use App\Models\Layer2\Team;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
