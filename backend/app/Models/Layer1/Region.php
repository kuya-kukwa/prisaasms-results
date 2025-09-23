<?php

namespace App\Models\Layer1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
class Region extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'code'];

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

    public function schools()
    {
        return $this->hasManyThrough(School::class, Province::class);
    }
}