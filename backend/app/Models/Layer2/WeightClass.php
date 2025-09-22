<?php

namespace App\Models\Layer2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer2\SportSubcategory;

class WeightClass extends Model
{
    use HasFactory;

    protected $fillable = ['sport_subcategory_id', 'name', 'min_weight', 'max_weight'];

    public function subcategory()
    {
        return $this->belongsTo(SportSubcategory::class, 'sport_subcategory_id');
    }
}
