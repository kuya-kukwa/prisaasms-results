<?php

namespace App\Models\Layer2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Layer2\Sport;
use App\Models\Layer1\User;

class WeightClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'name',
        'min_weight',
        'max_weight',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }

    // ✅ Accessor for formatted weight class label
    public function getRangeAttribute(): string
    {
        if (!is_null($this->min_weight) && !is_null($this->max_weight)) {
            return "{$this->min_weight}kg - {$this->max_weight}kg";
        }

        if (!is_null($this->max_weight)) {
            return "Under {$this->max_weight}kg";
        }

        if (!is_null($this->min_weight)) {
            return "Over {$this->min_weight}kg";
        }

        return "Open Weight";
    }
    public function officials()
{
    return $this->belongsToMany(
        User::class,
        'officials_weight_class',
        'weight_class_id',
        'official_id'
    )->withTimestamps()->withPivot('deleted_at');
}

}
