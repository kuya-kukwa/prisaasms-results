<?php

namespace App\Models\Layer2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer2\Sport;
use App\Models\Layer1\User;
use App\Models\Layer2\WeightClass;

class SportSubcategory extends Model
{
    use HasFactory;

    protected $fillable = ['sport_id', 'name', 'gender', 'format'];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function weightClasses()
    {
        return $this->hasMany(WeightClass::class);
    }
    public function officials()
{
    return $this->belongsToMany(
        User::class,
        'officials_sport_subcategory',
        'sport_subcategory_id',
        'official_id'
    )->withTimestamps()->withPivot('deleted_at');
}

}
