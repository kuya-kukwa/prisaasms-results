<?php

namespace App\Models\Layer3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Layer3\GameMatch;
use App\Models\Layer1\Tournament;
use App\Models\Layer1\Venue;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['tournament_id','division_id','sport_id','sport_subcategory_id','venue_id','start_date','end_date'];

    protected $casts = ['start_date'=>'datetime','end_date'=>'datetime'];

    public function tournament() { return $this->belongsTo(Tournament::class); }
    public function venue() { return $this->belongsTo(Venue::class); }
    public function division() { return $this->belongsTo(\App\Models\Layer1\Division::class); }
    public function sport() { return $this->belongsTo(\App\Models\Layer2\Sport::class); }
    public function subcategory() { return $this->belongsTo(\App\Models\Layer2\SportSubcategory::class,'sport_subcategory_id'); }
    public function matches() { return $this->hasMany(GameMatch::class); }
}
