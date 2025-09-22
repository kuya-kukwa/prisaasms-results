<?php

namespace App\Models\Layer1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer1\Tournament;

class SeasonYear extends Model
{
    use HasFactory;

    protected $fillable = ['year'];

    public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }
}
